<?php

namespace Qu\Config;

interface HydratorAwareInterface
{
    /**
     * @param HydratorInterface $hydrator
     * @return self
     */
    public function setHydrator(HydratorInterface $hydrator);

    /**
     * @return HydratorInterface
     */
    public function getHydrator();
}