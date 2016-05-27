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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed  $data
     * @param string $className
     * @return mixed
     */
    public function reconstruct($data, $className)
    {
        if (!is_array($data)) {
            return null;
        }

        // $className = 'Class[]'
        if ('[]' === substr($className, -2)) {
            $className = substr($className, 0, -2);
            $newData = [];

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $newData[$key] = $this->reconstructObject($value, $className);
                }
            }

            unset($data);

            return $newData;
        }

        return $this->reconstructObject($data, $className);
    }

    /**
     * @param mixed  $data
     * @param string $className
     * @return object
     */
    protected function reconstructObject($data, $className)
    {
        $object = $this->createObject($className);
        $map = $this->getClassMap($className);

        if ($object instanceof ReconstructableInterface) {
            if (false === $object->reconstruct($this, $data, $map)) {
                return $object;
            }
        }

        foreach ($data as $property => $value) {
            $accessType = $this->getAccessType($className, $property);

            if (!$accessType) {
                continue;
            }
            
            if (isset($map[$property])) {
                $value = $this->reconstruct($value, $map[$property]);
            }
            
            $this->writeProperty($object, $property, $value, $accessType);
        }

        return $object;
    }

    /**
     * @param string $className
     * @return object
     */
    protected function createObject($className)
    {
        return new $className();
    }

    /**
     * @param string $className
     * @return array
     */
    protected function getClassMap($className)
    {
        if (isset($this->options['map'][$className])) {
            return $this->options['map'][$className];
        }

        return [];
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
     */
    protected function writeProperty($object, $property, $value, $accessType)
    {
        switch ($accessType) {
            case self::ACCESS_TYPE_SETTER:
                $setter = 'set'.ucfirst($property);
                $object->$setter($value);
                break;

            case self::ACCESS_TYPE_PROPERTY:
                $object->$property = $value;
                break;

            case self::ACCESS_TYPE_MAGIC:
                $object->__set($property, $value);
        }
    }
}
