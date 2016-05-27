<?php

namespace Ruvents\DataReconstructor;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DataReconstructor
 */
class DataReconstructor
{
    const ACCESS_TYPE_SETTER = 'setter';

    const ACCESS_TYPE_PROPERTY = 'property';

    const ACCESS_TYPE_MAGIC = 'magic';

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected static $defaults = ['map' => []];

    /**
     * @var \ReflectionClass[]
     */
    protected static $reflections = [];

    /**
     * @var string[][]
     */
    protected static $accessTypes = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        /** @noinspection PhpUnusedParameterInspection */
        $resolver
            ->setDefaults(static::$defaults)
            ->setNormalizer('map', function (Options $options, $value) {
                return array_replace(static::$defaults['map'], $value);
            })
            ->setAllowedTypes('map', 'array');
    }

    /**
     * @param string $className
     * @return array
     */
    protected function getClassMap($className)
    {
        $className = ltrim($className, '\\');

        if (isset($this->options['map'][$className])) {
            return $this->options['map'][$className];
        }

        return [];
    }

    /**
     * @param mixed  $data
     * @param string $className
     * @return mixed
     */
    public function reconstruct($data, $className = null)
    {
        if (empty($data) || empty($className)) {
            return $data;
        }

        // Class[]
        if ('[]' === substr($className, -2)) {
            $className = substr($className, 0, -2);
            $map = $this->getClassMap($className);

            foreach ($data as &$value) {
                $value = $this->reconstructObject($value, $className, $map);
            }

            return $data;
        }

        // Class
        $map = $this->getClassMap($className);

        return $this->reconstructObject($data, $className, $map);
    }

    /**
     * @param mixed  $data
     * @param string $className
     * @param array  $map
     * @return object
     */
    protected function reconstructObject($data, $className, array $map)
    {
        $object = new $className;

        if ($object instanceof ReconstructableInterface) {
            if ($object instanceof ReconstructInterface) {
                @trigger_error(
                    sprintf('Interface %1$s\\ReconstructInterface is deprecated since version 1.0.8 and will be removed in version 2.0.0. Use %1$s\\ReconstructableInterface instead.',
                        __NAMESPACE__),
                    E_USER_DEPRECATED
                );
            }

            if (false === $object->reconstruct($this, $data, $map)) {
                return $object;
            }
        }

        if (empty($data) || !is_array($data)) {
            return $object;
        }

        foreach ($data as $property => $value) {
            $propertyClassName = isset($map[$property]) ? $map[$property] : null;
            $accessType = $this->getAccessType($className, $property);
            if ($accessType) {
                $this->writeProperty($object, $property, $value, $accessType, $propertyClassName);
            }
        }

        return $object;
    }

    /**
     * @param string|object $className
     * @param string        $property
     * @return string
     */
    protected function getAccessType($className, $property)
    {
        if (!isset(self::$accessTypes[$className][$property])) {
            $reflection = new \ReflectionClass($className);
            $setter = 'set'.ucfirst($property);

            switch (true) {
                case method_exists($className, $setter) && $reflection->getMethod($setter)->isPublic():
                    $accessType = self::ACCESS_TYPE_SETTER;
                    break;

                case property_exists($className, $property) && $reflection->getProperty($property)->isPublic():
                    $accessType = self::ACCESS_TYPE_PROPERTY;
                    break;

                case method_exists($className, '__set'):
                    $accessType = self::ACCESS_TYPE_MAGIC;
                    break;

                default:
                    $accessType = false;
            }

            self::$accessTypes[$className][$property] = $accessType;
        }

        return self::$accessTypes[$className][$property];
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     * @param string $accessType
     * @param string $propertyClassName
     */
    protected function writeProperty($object, $property, $value, $accessType, $propertyClassName)
    {
        $reconstructedValue = $this->reconstruct($value, $propertyClassName);

        switch ($accessType) {
            case self::ACCESS_TYPE_SETTER:
                $setter = 'set'.ucfirst($property);
                $object->$setter($reconstructedValue);
                break;

            case self::ACCESS_TYPE_PROPERTY:
                $object->$property = $reconstructedValue;
                break;

            case self::ACCESS_TYPE_MAGIC:
                $object->__set($reconstructedValue);
        }
    }
}
