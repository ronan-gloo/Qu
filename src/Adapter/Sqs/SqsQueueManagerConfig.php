<?php

namespace Qu\Adapter\Sqs;

use Qu\Config\HydratorAwareInterface;

class SqsQueueManagerConfig extends AbstractSqsConfig implements HydratorAwareInterface
{
    /**
     * Create a new queue silently if the requested if not found in definitions
     * @var bool
     */
    protected $createNotFound = false;

    /**
     * Queue name filtering while managing queues
     *
     * @var string
     */
    protected $queueNamePrefix;

    /**
     * @param boolean $createIfNotFound
     * @return self
     */
    public function setCreateNotFound($createIfNotFound)
    {
        $this->createNotFound = (bool) $createIfNotFound;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCreateNotFound()
    {
        return $this->createNotFound;
    }

    /**
     * @param string $queuePrefix
     * @return self
     */
    public function setQueueNamePrefix($queuePrefix)
    {
        $queuePrefix = trim($queuePrefix);
        $this->validateQueueName($queuePrefix);
        $this->queueNamePrefix = $queuePrefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getQueueNamePrefix()
    {
        return $this->queueNamePrefix;
    }
}