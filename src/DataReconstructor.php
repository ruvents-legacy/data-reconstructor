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
     * @return null|object|object[]
     */
    public function reconstruct($data, $className)
    {
        if ('[]' === substr($className, -2)) {
            return is_array($data) ? $this->reconstructArray($data, $className) : [];
        }

        return $this->reconstructObject($data, $className);
    }

    /**
     * @param array  $array
     * @param string $className
     * @return array
     */
    protected function reconstructArray(array $array, $className)
    {
        $className = substr($className, 0, -2);
        $newData = [];

        foreach ($array as $key => $data) {
            $newValue = $this->reconstructObject($data, $className);

            if (isset($newValue)) {
                $newData[$key] = $newValue;
            }
        }

        return $newData;
    }

    /**
     * @param mixed  $data
     * @param string $className
     * @return object
     */
    protected function reconstructObject($data, $className)
    {
        $map = isset($this->options['map'][$className]) ? $this->options['map'][$className] : [];

        if (!$object = $this->createObject($className, $data, $map)) {
            return null;
        }

        foreach ($data as $property => $value) {
            $accessType = $this->getAccessType($object, $property);

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
     * @param mixed  $data
     * @param array  $map
     * @return object|null
     */
    protected function createObject($className, &$data, array $map)
    {
        $reflection = new \ReflectionClass($className);

        switch (true) {
            case $reflection->implementsInterface(__NAMESPACE__.'\\ReconstructableInterface'):
                return new $className($data, $this, $map);

            case $className === 'DateTime' && is_string($data):
            case $reflection->isSubclassOf('DateTime') && is_string($data):
                $object = new \DateTime($data);
                $data = [];

                return $object;

            case is_array($data):
                return new $className;

            default:
                return null;
        }
    }

    /**
     * @param object $object
     * @param string $property
     * @return string
     */
    protected function getAccessType($object, $property)
    {
        $className = get_class($object);

        if (!isset(self::$accessTypes[$className][$property])) {
            $reflection = new \ReflectionClass($object);
            $setter = 'set'.ucfirst($property);

            switch (true) {
                case $reflection->hasMethod($setter) && $reflection->getMethod($setter)->isPublic():
                    $accessType = self::ACCESS_TYPE_SETTER;
                    break;

                case $reflection->hasProperty($property) && $reflection->getProperty($property)->isPublic():
                    $accessType = self::ACCESS_TYPE_PROPERTY;
                    break;

                case $reflection->hasMethod('__set'):
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
