<?php

namespace Qu\Adapter\ZendJobQueue;

use Qu\Exception\QueueNotFoundException;
use Qu\Exception\RuntimeException;
use Qu\Exception\UnsupportedFeatureException;
use Qu\Queue\QueueInterface;
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
     * @return void
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function get($options)
    {
        throw new UnsupportedFeatureException('Zend job queue does supports queue retrieval');
    }

    /**
     * @param $options
     * @return void
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function create($options)
    {
        throw new UnsupportedFeatureException('Zend job queue does not supports queue creation');
    }

    /**
     * @param QueueInterface $queue
     * @param $data
     * @throws \Qu\Exception\UnsupportedFeatureException
     * @return void
     */
    public function update(QueueInterface $queue, $data = null)
    {
        throw new UnsupportedFeatureException('Zend job queue does not supports queue update');
    }

    /**
     * Permanently delete a queue.
     *
     * @param QueueInterface $queue
     * @throws \Qu\Exception\UnsupportedFeatureException
     * @return void
     */
    public function remove(QueueInterface $queue)
    {
        throw new UnsupportedFeatureException('Zend job queue does not supports queue deletion');
    }

    /**
     * Remove all available from the queue.
     * implementations will assume that busy|invisible elements mst not be removed
     *
     * @param QueueInterface $queue
     * @return void
     * @throws QueueNotFoundException   If the queue cannot be found
     * @throws RuntimeException         Otherwise
     */
    public function flush(QueueInterface $queue)
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