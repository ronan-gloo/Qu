<?php

namespace Qu\Adapter\Sqs;

use Qu\Config\HydratorAwareInterface;
use Qu\Config\HydratorAwareTrait;

class SqsQueueManagerConfig implements HydratorAwareInterface
{
    use HydratorAwareTrait;

    /**
     * Required account id
     *
     * @var integer
     */
    protected $accountId;

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
     * @param array $config
     * @internal param array $definitions
     */
    public function __construct($config = [])
    {
        if ($config) {
            $this->hydrate($config);
        }
    }

    /**
     * @param int $accountId
     * @return self
     */
    public function setAccountId($accountId)
    {
        $this->accountId = (int) $accountId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

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
     * @param string $queuesPrefix
     * @return self
     */
    public function setQueueNamePrefix($queuesPrefix)
    {
        $this->queueNamePrefix = (string) $queuesPrefix;
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