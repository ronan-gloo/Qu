<?php

namespace Qu\Adapter\Beanstalk;

use Pheanstalk\Pheanstalk;
use Qu\Config\HydratorAwareInterface;
use Qu\Config\HydratorAwareTrait;

class BeanStalkQueueConfig implements HydratorAwareInterface
{
    use HydratorAwareTrait;

    /**
     * @var string
     */
    protected $tube = Pheanstalk::DEFAULT_TUBE;

    /**
     * Default timeout to keep connected to the queue
     *
     * @var int
     */
    protected $waitTimeout = 10;

    /**
     * Seconds a job can be reserved for
     *
     * @var int
     */
    protected $timeToRun = Pheanstalk::DEFAULT_TTR;

    /**
     * Global priority for messages, from 0 to 4294967295
     *
     * @var int
     */
    protected $priority = Pheanstalk::DEFAULT_PRIORITY;

    /**
     * Delay for message visibility
     *
     * @var int
     */
    protected $delay = Pheanstalk::DEFAULT_DELAY;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            $this->hydrate($config);
        }
    }

    /**
     * @param string $tube
     * @return self
     */
    public function setTube($tube)
    {
        $this->tube = (string) $tube;
        return $this;
    }

    /**
     * @return string
     */
    public function getTube()
    {
        return $this->tube;
    }

    /**
     * @param int $reserveTimeout
     * @return self
     */
    public function setWaitTimeout($reserveTimeout)
    {
        $this->waitTimeout = (int) $reserveTimeout;
        return $this;
    }

    /**
     * @return int
     */
    public function getWaitTimeout()
    {
        return $this->waitTimeout;
    }

    /**
     * @param int $delay
     * @return self
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
        return $this;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
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
     * @param int $timeToRun
     * @return self
     */
    public function setTimeToRun($timeToRun)
    {
        $this->timeToRun = $timeToRun;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeToRun()
    {
        return $this->timeToRun;
    }
} 