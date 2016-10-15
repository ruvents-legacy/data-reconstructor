<?php

namespace Ruvents\DataReconstructor\Helper;

use ReflectionClass;

class TypeHelperTest extends \PHPUnit_Framework_TestCase
{
    private $typeHelper;

    public function setUp()
    {
        $this->typeHelper = $this->getObjectForTrait('Ruvents\\DataReconstructor\\Helper\\TypeHelper');
    }

    public function testIsTypeStrict()
    {
        $this->assertFalse($this->callMethod($this->typeHelper, 'isTypeStrict', ['string']));
        $this->assertTrue($this->callMethod($this->typeHelper, 'isTypeStrict', ['!int']));
    }

    public function testIsTypeArray()
    {
        $this->assertFalse($this->callMethod($this->typeHelper, 'isTypeArray', ['string']));
        $this->assertTrue($this->callMethod($this->typeHelper, 'isTypeArray', ['int[]']));
    }

    public function testIsTypeFlat()
    {
        $this->assertFalse($this->callMethod($this->typeHelper, 'isTypeFlat', ['a']));
        $this->assertFalse($this->callMethod($this->typeHelper, 'isTypeFlat', ['array']));
    }

    protected function callMethod($obj, $name, array $args)
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
