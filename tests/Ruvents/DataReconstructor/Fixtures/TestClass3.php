<?php

namespace Ruvents\DataReconstructor\Fixtures;

use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\DataReconstructor\ReconstructInterface;

/**
 * Class TestClass3
 * @package Ruvents\DataReconstructor\Fixtures
 */
class TestClass3 implements ReconstructInterface
{
    /**
     * @var array
     */
    public $data;

    public $key;

    /**
     * @inheritdoc
     */
    public function reconstruct($data, DataReconstructor $dataReconstructor)
    {
        $this->data = $data;

        return false;
    }
}
