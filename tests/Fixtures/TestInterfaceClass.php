<?php

namespace Ruvents\DataReconstructor\Fixtures;

use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\DataReconstructor\ReconstructableInterface;

class TestInterfaceClass implements ReconstructableInterface
{
    public $property;
    
    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map)
    {
        $data['property'] = 'changed';
    }
}
