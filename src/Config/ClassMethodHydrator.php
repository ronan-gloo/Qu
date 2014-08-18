<?php

namespace Qu\Config;

use Qu\Exception\InvalidArgumentException;

class ClassMethodHydrator implements HydratorInterface
{
    /**
     * @param array $array
     * @param mixed $object
     * @throws \Qu\Exception\InvalidArgumentException
     * @return mixed $object
     */
    public function hydrate($array, $object)
    {
        if (! is_object($object)) {
            throw new InvalidArgumentException('Second argument must be an object');
        }
        if (! is_array($array) || ! $array instanceof \Traversable) {
            throw new InvalidArgumentException('First argument must be traversable');
        }

        foreach ($array as $key => $item) {
            $setter = 'set' . str_replace('_', '', $key);
            if (is_callable([$object, $setter])) {
                $object->$setter($item);
            }
        }
        return $object;
    }
}