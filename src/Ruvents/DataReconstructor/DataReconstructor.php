<?php

namespace Ruvents\DataReconstructor;

/**
 * Class DataReconstructor
 * @package Ruvents\DataReconstructor
 */
class DataReconstructor
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var ClassHelper[]
     */
    protected $classHelpers = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
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
                $value = $this->reconstruct($value, substr($className, 0, -2));
            }

            return $data;
        }

        // Class
        $object = new $className;
        $classHelper = $this->getClassHelper($className);
        foreach ($data as $offset => $value) {
            $classType = $classHelper->getPropertyClassType($offset);
            $value = $this->reconstruct($value, $classType);
            $classHelper->setProperty($object, $offset, $value);
        }

        return $object;
    }

    /**
     * @param string $className
     * @return ClassHelper
     */
    public function getClassHelper($className)
    {
        if (!isset($this->classHelpers[$className])) {
            $this->classHelpers[$className] = new ClassHelper($className);
        }

        return $this->classHelpers[$className];
    }
}
