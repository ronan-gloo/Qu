<?php

namespace Qu\Adapter\ZendJobQueue;

use Qu\Exception\OperationException;
use Qu\Exception\RuntimeException;
use Qu\Exception\UnsupportedFeatureException;
use Qu\Iterator\QueueIteratorAwareTrait;
use Qu\Message\MessageInterface;
use Qu\Queue\QueueInterface;
use Qu\Serializer\SerializerAwareInterface;
use Qu\Serializer\SerializerAwareTrait;

class ZendQueue implements QueueInterface, SerializerAwareInterface
{
    use SerializerAwareTrait, QueueIteratorAwareTrait;

    /**
     * @var \ZendJobQueue
     */
    protected $client;

    /**
     * @var ZendQueueConfig
     */
    protected $config;

    /**
     * @param \ZendJobQueue $client
     * @param array $config
     */
    public function __construct(\ZendJobQueue $client, $config = [])
    {
        $this->client = $client;
        $this->config = $config instanceof ZendQueueConfig ? $config : new ZendQueueConfig($config);
    }

    /**
     * Insert a new message at the top of the queue
     *
     * @param MessageInterface $message
     * @return void
     */
    public function enqueue(MessageInterface $message)
    {
        $data  = $this->getSerializer()->serialize($message, $this->config);
        $jobId = call_user_func_array([$this->client, 'createHttpJob'], $data);

        $jobId and $message->setId($jobId);
    }

    /**
     * Extract the message from the queue.
     * Note that the message must be permanently removed from the queue
     *
     * @return MessageInterface
     */
    public function dequeue()
    {
        // result is the message body
        $jobData = $this->client->getCurrentJobParams();
        $message = null;

        if ($jobData) {
            $message = $this->getSerializer()->unserialize($jobData);
            $message->setId($this->client->getCurrentJobId());
        }

        return $message;
    }

    /**
     * To successfully run this action, you may know few concerns:
     * The job must exists in queue, and "available", that means:
     *  - Not currently running
     *  - Not scheduled, waiting or pending
     *
     * Note that The MessageInterface will acquire a new id on success.
     *
     * @param MessageInterface $message
     * @throws \Qu\Exception\RuntimeException
     * @throws \Qu\Exception\OperationException
     * @return MessageInterface|void
     */
    public function requeue(MessageInterface $message)
    {
        try {
            $jobStatus = $this->client->getJobStatus($message->getId());
        }
        catch (\Exception $e) {
            $messageId = $message->getId() === null ? 'NULL' : $message->getId();
            throw new RuntimeException(sprintf('Cannot requeue job with id "%s"', $messageId), null, $e);
        }

        // false means job has been deleted, so we need to acquire a new id ?
        if (false === $jobStatus) {
            throw new OperationException('Cannot requeue. Unknown message with id ' . $message->getId());
        }

        switch ($jobStatus) {
            case \ZendJobQueue::JOB_STATUS_SCHEDULED:
            case \ZendJobQueue::JOB_STATUS_PENDING:
            case \ZendJobQueue::JOB_STATUS_RUNNING:
            case \ZendJobQueue::JOB_STATUS_WAITING_PREDECESSOR:
                $newMessage = null;
                break;

            default:
                $newMessage = clone $message;
                $this->enqueue($newMessage);
        }

        return $newMessage;
    }

    /**
     * @return \Qu\Iterator\QueueIteratorInterface|\Traversable|void
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function getIterator()
    {
        throw new UnsupportedFeatureException('Zend Job Queue does not allow you to iterates over queue items');
    }

    /**
     * Delete a particular message
     *
     * @param MessageInterface $message
     * @throws \Qu\Exception\RuntimeException
     * @return void
     */
    public function remove(MessageInterface $message)
    {
        try {
            $this->client->removeJob($message->getId());
        }
        catch (\Exception $e) {
            throw new RuntimeException('Error while removing job with id ' . $message->getId(), null, $e);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return (int) $this->client->getStatistics()['waiting'];
    }
}