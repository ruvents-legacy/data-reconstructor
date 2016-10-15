<?php

namespace Ruvents\DataReconstructor\Helper;

trait TypeHelper
{
    /**
     * @return array
     */
    public static function getFlatTypes()
    {
        return ['null', 'boolean', 'bool', 'integer', 'int', 'double', 'float', 'string'];
    }

    /**
     * @param string $type
     * @return bool
     */
    private function isTypeStrict($type)
    {
        return substr($type, 0, 1) === '!';
    }

    /**
     * @param string $type
     * @return bool
     */
    private function isTypeArray($type)
    {
        return substr($type, -2) === '[]';
    }

    /**
     * @param string $type
     * @return bool
     */
    private function isTypeFlat($type)
    {
        return in_array($type, self::getFlatTypes(), true);
    }
}
