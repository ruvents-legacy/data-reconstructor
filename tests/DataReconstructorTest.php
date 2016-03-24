<?php

namespace Ruvents\DataReconstructor;

class DataReconstructorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataReconstructor
     */
    private $reconstructor;

    public function setUp()
    {
        $this->reconstructor = new DataReconstructor();
    }

    public function testSimple()
    {
        $className = 'Ruvents\DataReconstructor\Fixtures\TestClass';
        $data = [
            'publicProperty' => 'publicValue',
            'setterProperty' => 'Value',
            'magicProperty' => 'Value',
            'nonexistentProperty' => 'nonexistentValue',
        ];

        $object = $this->reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('publicValue', 'publicProperty', $object);
        $this->assertAttributeEquals('setterValue', 'setterProperty', $object);
        $this->assertAttributeEquals('magicValue', 'magicProperty', $object);
        $this->assertObjectNotHasAttribute('nonexistentProperty', $object);
    }

    public function testInterface()
    {
        $className = 'Ruvents\DataReconstructor\Fixtures\TestImplemClass';
        $data = ['property' => 'value'];

        $object = $this->reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('changed', 'property', $object);
    }

    public function testInterfaceInterrupted()
    {
        $className = 'Ruvents\DataReconstructor\Fixtures\TestImplemInterClass';
        $data = ['property' => 'propertyValue', 'emptyProperty' => 'emptyPropertyValue'];

        $object = $this->reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('propertyValue', 'property', $object);
        $this->assertAttributeEmpty('emptyProperty', $object);
    }
}
