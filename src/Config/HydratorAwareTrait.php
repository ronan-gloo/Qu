<?php

namespace Qu\Config;

trait HydratorAwareTrait
{
    protected $arrayHydrator;

    /**
     * @param HydratorInterface $hydrator
     * @return $this
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->arrayHydrator = $hydrator;

        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (null === $this->arrayHydrator) {
            $this->arrayHydrator = new ClassMethodHydrator();
        }

        return $this->arrayHydrator;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function hydrate($array)
    {
        $this->getHydrator()->hydrate($array, $this);
        return $this;
    }
}