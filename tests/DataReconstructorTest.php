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

    public function testSimpleClass()
    {
        $className = 'Ruvents\DataReconstructor\Fixtures\TestClass';
        $data = [
            'property1' => 'value1',
            'property2' => 'value2',
            'property3' => 'value3',
        ];

        $object = $this->reconstructor->reconstruct($data, $className);

        for ($i = 1; $i <= 3; $i++) {
            $this->assertAttributeEquals($data["property$i"], "property$i", $object);
        }
    }
}
