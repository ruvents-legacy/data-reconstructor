<?php

namespace Ruvents\DataReconstructor;

/**
 * Interface ReconstructInterface
 */
interface ReconstructableInterface
{
    /**
     * @param DataReconstructor $dataReconstructor
     * @param mixed             $data
     * @param array             $map
     * @return false|void
     */
    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map);
}
