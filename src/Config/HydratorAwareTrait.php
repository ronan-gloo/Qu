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
     * @param array $data
     * @return $this
     */
    public function hydrate($data)
    {
        $this->getHydrator()->hydrate($data, $this);

        return $this;
    }
}