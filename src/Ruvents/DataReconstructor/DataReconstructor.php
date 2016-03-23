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
        $this->options = array_merge_recursive($this->options, $options);
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param mixed  $data
     * @param string $className
     * @return mixed
     */
    public function reconstruct($data, $className = null)
    {
        if (!is_array($data) || !isset($className)) {
            return $data;
        }

        // Class[]
        if (substr($className, -2) === '[]') {
            foreach ($data as &$value) {
                $value = $this->reconstructObject($value, substr($className, 0, -2));
            }

            return $data;
        }

        // Class
        return $this->reconstructObject($data, $className);
    }

    /**
     * @param array  $data
     * @param string $className
     * @return object
     */
    protected function reconstructObject(array $data, $className)
    {
        $className = ltrim($className, '\\');
        $object = new $className;

        if ($object instanceof ReconstructInterface) {
            $object->reconstruct($this, $data);

            return $object;
        }

        $map = $this->getClassMap($className);

        foreach ($data as $property => $value) {
            $this->writeProperty($object, $property, $value, $map);
        }

        return $object;
    }

    /**
     * @param string $className
     * @return array
     */
    protected function getClassMap($className)
    {
        if (isset($this->options['map'][$className])) {
            return $this->options['map'][$className];
        } else {
            return [];
        }
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     * @param array  $map
     */
    protected function writeProperty($object, $property, $value, array $map = [])
    {
        if (!$this->propertyAccessor->isWritable($object, $property)) {
            return;
        }

        $propertyClassName = isset($map[$property]) ? $map[$property] : null;
        $value = $this->reconstruct($value, $propertyClassName);

        $this->propertyAccessor->setValue($object, $property, $value);
    }
}
