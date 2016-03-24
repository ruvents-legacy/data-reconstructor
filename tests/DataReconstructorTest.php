<?php

namespace Ruvents\DataReconstructor;

class DataReconstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $reconstructor = new DataReconstructor();
        $className = __NAMESPACE__.'\Fixtures\TestClass';
        $data = [
            'publicProperty' => 'publicValue',
            'setterProperty' => 'Value',
            'magicProperty' => 'Value',
            'nonexistentProperty' => 'nonexistentValue',
        ];

        $object = $reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('publicValue', 'publicProperty', $object);
        $this->assertAttributeEquals('setterValue', 'setterProperty', $object);
        $this->assertAttributeEquals('magicValue', 'magicProperty', $object);
        $this->assertObjectNotHasAttribute('nonexistentProperty', $object);
    }

    public function testInterface()
    {
        $reconstructor = new DataReconstructor();
        $className = __NAMESPACE__.'\Fixtures\TestInterfaceClass';
        $data = ['property' => 'value'];

        $object = $reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('changed', 'property', $object);
    }

    public function testInterfaceInterrupted()
    {
        $reconstructor = new DataReconstructor();
        $className = __NAMESPACE__.'\Fixtures\TestInterruptedInterfaceClass';
        $data = ['property' => 'propertyValue', 'emptyProperty' => 'emptyPropertyValue'];

        $object = $reconstructor->reconstruct($data, $className);

        $this->assertAttributeEquals('propertyValue', 'property', $object);
        $this->assertAttributeEmpty('emptyProperty', $object);
    }

    public function testNestedClasses()
    {
        $reconstructor = new DataReconstructor(['map' => [
            __NAMESPACE__.'\Fixtures\TestClassLevel1' => [
                'level2' => __NAMESPACE__.'\Fixtures\TestClassLevel2',
            ],
            __NAMESPACE__.'\Fixtures\TestClassLevel2' => [
                'level2' => __NAMESPACE__.'\Fixtures\TestClassLevel3[]',
            ],
        ]]);

        $object = $reconstructor->reconstruct([
            'level2' => [
                'level3' => [
                    ['property' => 0],
                    ['property' => 1],
                ],
            ],
        ], __NAMESPACE__.'\Fixtures\TestClassLevel1');

        $this->assertEquals(0, $object->level2->level3[0]['property']);
        $this->assertEquals(1, $object->level2->level3[1]['property']);
    }
}
