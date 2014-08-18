<?php

namespace Qu\Adapter\ZendJobQueue;

use Qu\Config\HydratorAwareInterface;
use Qu\Config\HydratorAwareTrait;

class ZendQueueConfig implements HydratorAwareInterface
{
    use HydratorAwareTrait;

    /**
     * @var int
     */
    protected $priority = \ZendJobQueue::PRIORITY_NORMAL;

    /**
     * Time after n seconds when job should be executed
     *
     * @var null
     */
    protected $scheduleDelay = 0;

    /**
     * Time, in seconds, to wait for incoming messages
     *
     * @var int
     */
    protected $jobTimeout = 10;

    /**
     * callback urls can be set here, as global configuration for your jobs.
     * For particular callbacks, see the Meta key defined in serializer
     *
     * @var string
     */
    protected $callbackUrl = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        if ($options) {
            $this->hydrate($options);
        }
    }

    /**
     * @param int $jobTimeout
     * @return self
     */
    public function setJobTimeout($jobTimeout)
    {
        $this->jobTimeout = $jobTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getJobTimeout()
    {
        return $this->jobTimeout;
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param null $scheduleDelay
     * @return self
     */
    public function setScheduleDelay($scheduleDelay)
    {
        $this->scheduleDelay = $scheduleDelay;
        return $this;
    }

    /**
     * @return null
     */
    public function getScheduleDelay()
    {
        return $this->scheduleDelay;
    }

    /**
     * @param string $callbackUrl
     * @return self
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @param string $name
     * @return ZendQueueConfig
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}