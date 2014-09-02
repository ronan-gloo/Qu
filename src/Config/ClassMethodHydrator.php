<?php

namespace Qu\Config;

use Qu\Exception\InvalidArgumentException;

class ClassMethodHydrator implements HydratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function hydrate($traversable, $object)
    {
        if (! is_object($object)) {
            throw new InvalidArgumentException('Second argument must be an object');
        }
        if (! is_array($traversable) && ! $traversable instanceof \Traversable) {
            throw new InvalidArgumentException('First argument must be traversable');
        }

        foreach ($traversable as $key => $item) {
            $setter = 'set' . str_replace('_', '', $key);
            if (is_callable([$object, $setter])) {
                $object->$setter($item);
            }
        }

        return $object;
    }
}