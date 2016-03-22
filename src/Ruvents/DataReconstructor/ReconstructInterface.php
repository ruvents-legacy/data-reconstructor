<?php

namespace Ruvents\DataReconstructor;

/**
 * Interface ReconstructInterface
 * @package Ruvents\DataReconstructor
 */
interface ReconstructInterface
{
    /**
     * @param DataReconstructor $dataReconstructor
     * @param array             $data
     */
    public function __construct(DataReconstructor $dataReconstructor, array $data);
}
