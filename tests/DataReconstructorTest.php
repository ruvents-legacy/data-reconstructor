<?php

namespace Ruvents\DataReconstructor;

class DataReconstructorTest extends \PHPUnit_Framework_TestCase
{
    static $flatTypes = ['null', 'bool', 'int', 'float', 'string'];

    static $supportedFlatTypes = ['null', 'boolean', 'bool', 'integer', 'int', 'double', 'float', 'string'];

    /**
     * @var DataReconstructor
     */
    private $dr;

    private $flatValues = [];

    public function setUp()
    {
        $this->dr = new DataReconstructor();

        foreach (self::$flatTypes as $flatType) {
            $value = 'a';
            $this->flatValues[] = settype($value, $flatType);
        }
    }

    public function testFlat()
    {
        foreach ($this->flatValues as $flatValue) {
            foreach (self::$supportedFlatTypes as $type) {
                $expected = $flatValue;
                settype($expected, $type);

                $this->assertEquals($expected, $this->dr->reconstruct($flatValue, $type));
            }
        }
    }

    public function testArray()
    {
        $array = [[null, '', 4], ['a', []]];

        $this->assertEquals(
            $array,
            $this->dr->reconstruct($array, 'array[]')
        );
    }

    public function testArrayFlat()
    {
        $this->assertEquals(
            [[0, 0, 1, 2], [3, 4]],
            $this->dr->reconstruct([[null, '', '1', '2'], ['3', 4]], 'int[][]')
        );
    }
}
