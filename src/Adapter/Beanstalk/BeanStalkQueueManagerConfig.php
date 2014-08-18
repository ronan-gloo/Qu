<?php

namespace Qu\Adapter\Beanstalk;

use Qu\Config\HydratorAwareInterface;
use Qu\Config\HydratorAwareTrait;

class BeanStalkQueueManagerConfig implements HydratorAwareInterface
{
    use HydratorAwareTrait;

    /**
     * @var string
     */
    protected $tubeNamePrefix;

    /**
     * @var bool
     */
    protected $createNotFound = true;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        if ($config) {
            $this->hydrate($config);
        }
    }

    /**
     * @param mixed $tubeNamePrefix
     * @return self
     */
    public function setTubeNamePrefix($tubeNamePrefix)
    {
        $this->tubeNamePrefix = (string) $tubeNamePrefix;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTubeNamePrefix()
    {
        return $this->tubeNamePrefix;
    }

    /**
     * @param boolean $createNotFound
     * @return self
     */
    public function setCreateNotFound($createNotFound)
    {
        $this->createNotFound = $createNotFound;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCreateNotFound()
    {
        return $this->createNotFound;
    }
}