<?php

namespace Qu\Config;

interface HydratorInterface
{
    /**
     * Hydrate object from array
     *
     * @param \Traversable|array $data    Set of property / data
     * @param mixed $object               Object to hydrate
     * @return void
     */
    public function hydrate($data, $object);
}