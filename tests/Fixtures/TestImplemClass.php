<?php

namespace Ruvents\DataReconstructor\Fixtures;

use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\DataReconstructor\ReconstructInterface;

class TestImplemClass implements ReconstructInterface
{
    public $property;
    
    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map)
    {
        $data['property'] = 'changed';
    }
}
