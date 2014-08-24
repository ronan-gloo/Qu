<?php

namespace Qu\Adapter\ZendJobQueue;

use Qu\Exception\QueueNotFoundException;
use Qu\Exception\RuntimeException;
use Qu\Exception\UnsupportedFeatureException;
use Qu\Queue\QueueAdapterInterface;
use Qu\Queue\QueueManagerInterface;

class ZendQueueManager implements QueueManagerInterface
{
    protected $client;

    /**
     * @param \ZendJobQueue $client
     */
    public function __construct(\ZendJobQueue $client)
    {
        $this->client = $client;
    }

    /**
     * @param mixed $options
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function get($options)
    {
        throw new UnsupportedFeatureException('Zend job queue does supports queue retrieval');
    }

    /**
     * @param $options
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function create($options)
    {
        throw new UnsupportedFeatureException('Zend job queue does not supports queue creation');
    }

    /**
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function update(QueueAdapterInterface $queue, $data = null)
    {
        throw new UnsupportedFeatureException('Zend job queue does not supports queue update');
    }

    /**
     * @param QueueInterface $queue
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function remove(QueueAdapterInterface $queue)
    {
        throw new UnsupportedFeatureException('Zend job queue does not supports queue deletion');
    }

    /**
     * {@inheritDoc}
     */
    public function flush(QueueAdapterInterface $queue)
    {
        $jobs = $this->client->getJobsList();

        foreach ($jobs as $job) {
            // do not flush currently running jobs
            if ((int) $job['status'] !== \ZendJobQueue::STATUS_RUNNING) {
                $this->client->removeJob($job['id']);
            }
        }
    }
}