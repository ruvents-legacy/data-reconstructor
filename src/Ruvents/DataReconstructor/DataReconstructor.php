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
        if (!isset($className)) {
            return $data;
        }

        // Class[]
        if (substr($className, -2) === '[]') {
            foreach ($data as &$value) {
                $value = $this->reconstructObject(substr($className, 0, -2), $value);
            }

            return $data;
        }

        // Class
        return $this->reconstructObject($className, $data);
    }

    /**
     * @param string $className
     * @param mixed  $data
     * @return object
     */
    protected function reconstructObject($className, $data = null)
    {
        $object = new $className($data);

        if ($object instanceof ReconstructInterface) {
            if ($object->reconstruct($data, $this) === false) {
                return $object;
            }
        }

        if (empty($data) || !is_array($data)) {
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
        $className = ltrim($className, '\\');

        if (isset($this->options['map'][$className])) {
            return $this->options['map'][$className];
        }

        return [];
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
