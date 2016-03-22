<?php

namespace Ruvents\DataReconstructor;

/**
 * Class ClassHelper
 * @package Ruvents\DataReconstructor
 */
class ClassHelper
{
    /**
     * @var array
     */
    private static $nonClassTypes = [
        'null', 'boolean', 'bool', 'false', 'true',
        'integer', 'int', 'float', 'string',
        'array', 'object',
        'callback', 'resource', 'mixed',
    ];

    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @var array
     */
    private $propertyClassTypes = [];

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->reflection = new \ReflectionClass($className);
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function getPropertyClassType($name)
    {
        if (isset($this->propertyClassTypes[$name])) {
            return $this->propertyClassTypes[$name];
        }

        $doc = $this->reflection->getProperty($name)->getDocComment();

        $varTypesStr = implode('|', self::$nonClassTypes);
        preg_match("/@var[\h]+(?:(?:$varTypesStr)(?:\[\])?\|?)*(([\w\\\]+)(\[\])?)?/", $doc, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        $classType = $matches[2] === 'self' ? $this->reflection->getName() : $matches[2];
        $classType = '\\'.ltrim($classType, '\\').(isset($matches[3]) ? '[]' : '');

        return $this->propertyClassTypes[$name] = $classType;
    }
}
