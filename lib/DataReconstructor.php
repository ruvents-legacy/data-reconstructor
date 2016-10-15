<?php

namespace Ruvents\DataReconstructor;

class DataReconstructor implements DataReconstructorInterface
{
    use Helper\TypeHelper;
    use Helper\AccessorHelper;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $classMap;

    /**
     * @param array $options
     * @param array $classMap
     */
    public function __construct(array $options = [], array $classMap = [])
    {
        $this->options = $options;
        $this->classMap = $classMap;
    }

    /**
     * @param mixed  $data
     * @param string $type
     * @return mixed
     */
    public function reconstruct($data, $type)
    {
        switch (true) {
            case $this->isTypeArray($type):
                foreach ($data as &$value) {
                    $value = $this->reconstruct($value, substr($type, 0, -2));
                }

                break;

            case $this->isTypeFlat($type):
                settype($data, $type);

                break;

            case $type === 'array':
                break;

            default:
                $object = new $type;

                foreach ($this->classMap[$type] as $accessor => $accessorType) {
                    $isMethod = $this->isAccessorMethod($accessor);
                    $accessor = $isMethod ? substr($accessor, 0, -2) : $accessor;

                    $value = $this->reconstruct($data[$accessor], $accessorType);

                    if ($isMethod) {
                        $object->$accessor($value);
                    } else {
                        $object->$accessor = $value;
                    }
                }

                $data = $object;
        }

        return $data;
    }
}
