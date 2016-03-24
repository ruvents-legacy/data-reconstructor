<?php

namespace Ruvents\DataReconstructor;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class DataReconstructor
 * @package Ruvents\DataReconstructor
 */
class DataReconstructor
{
    /**
     * @var array
     */
    protected $options = [
        'map' => [],
    ];

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_replace_recursive($this->options, $options);
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
        if (!isset($className)) {
            return $data;
        }

        // Class[]
        if (substr($className, -2) === '[]') {
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

        if ($object instanceof ReconstructInterface) {
            if (false === $object->reconstruct($this, $data, $map)) {
                return $object;
            }
        }

        if (empty($data) || !is_array($data)) {
            return $object;
        }

        foreach ($data as $property => $value) {
            $propertyClassName = isset($map[$property]) ? $map[$property] : null;
            $this->writeProperty($object, $property, $value, $propertyClassName);
        }

        return $object;
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     * @param string $propertyClassName
     */
    protected function writeProperty($object, $property, $value, $propertyClassName)
    {
        if (!$this->propertyAccessor->isWritable($object, $property)) {
            return;
        }

        $value = $this->reconstruct($value, $propertyClassName);

        $this->propertyAccessor->setValue($object, $property, $value);
    }
}
