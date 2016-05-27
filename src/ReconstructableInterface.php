<?php

namespace Ruvents\DataReconstructor;

/**
 * Interface ReconstructInterface
 */
interface ReconstructableInterface
{
    /**
     * @param mixed             $data
     * @param DataReconstructor $dataReconstructor
     * @param array             $map
     */
    public function __construct(&$data, DataReconstructor $dataReconstructor, array $map);
}
