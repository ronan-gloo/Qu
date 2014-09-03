<?php

namespace Qu\Job;

use Qu\Exception\RuntimeException;

class JobExecution implements JobExecutionInterface
{
    const STATUS_UNPROCESSED = 0;
    const STATUS_SUCCEED     = 1;
    const STATUS_FAILED      = 2;

    /**
     * @var \Qu\Job\JobInterface
     */
    protected $job;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @param JobInterface $job
     */
    public function __construct(JobInterface $job)
    {
        $this->job    = $job;
        $this->status = static::STATUS_UNPROCESSED;
    }

    /**
     * @param mixed $result
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param null $result
     * @throws \Qu\Exception\RuntimeException
     * @return $this
     */
    public function setFailed($result = null)
    {
        if ($this->processed()) {
            throw new RuntimeException('Cannot set a status twice');
        }

        $this->status = static::STATUS_FAILED;
        if (null !== $result) {
            $this->setResult($result);
        }

        return $this;
    }

    /**
     * @param null $result
     * @throws \Qu\Exception\RuntimeException
     * @return $this
     */
    public function setSucceed($result = null)
    {
        if ($this->processed()) {
            throw new RuntimeException('Cannot set a status twice');
        }

        $this->status = static::STATUS_SUCCEED;
        if (null !== $result) {
            $this->setResult($result);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function processed()
    {
        return static::STATUS_UNPROCESSED !== $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function failed()
    {
        return static::STATUS_FAILED === $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function succeed()
    {
        return static::STATUS_SUCCEED === $this->status;
    }
} 