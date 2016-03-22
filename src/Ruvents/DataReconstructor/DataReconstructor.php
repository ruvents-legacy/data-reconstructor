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
    protected $options = [];

    /**
     * @var ClassHelper[]
     */
    protected $classHelpers = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
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

        // for @var Class[]
        if (substr($className, -2) === '[]') {
            foreach ($data as &$value) {
                $value = $this->reconstructObject($value, substr($className, 0, -2));
            }

            return $data;
        }

        // for @var Class
        return $this->reconstructObject($data, $className);
    }

    /**
     * @param array  $data
     * @param string $className
     * @return object
     */
    protected function reconstructObject(array $data, $className)
    {
        $classHelper = $this->getClassHelper($className);
        $reconstructInterface = 'Ruvents\DataReconstructor\ReconstructInterface';

        if ($classHelper->getReflection()->implementsInterface($reconstructInterface)) {
            return new $className($this, $data);
        }

        $object = new $className;

        foreach ($data as $offset => $value) {
            $classType = $classHelper->getPropertyClassType($offset);
            $value = $this->reconstruct($value, $classType);
            $classHelper->setProperty($object, $offset, $value);
        }

        return $object;
    }
}
