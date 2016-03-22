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
    private $data;

    /**
     * @inheritdoc
     */
    public function __construct(DataReconstructor $dataReconstructor, array $data)
    {
        $this->data = $data;
    }
}
