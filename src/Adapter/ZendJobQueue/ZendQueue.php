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
     * {@inheritDoc}
     */
    public function enqueue(MessageInterface $message)
    {
        $data  = $this->getSerializer()->serialize($message, $this->config);
        $jobId = call_user_func_array([$this->client, 'createHttpJob'], $data);

        $jobId and $message->setId($jobId);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * @throws \Qu\Exception\UnsupportedFeatureException
     */
    public function getIterator()
    {
        throw new UnsupportedFeatureException('Zend Job Queue does not allow you to iterates over queue items');
    }

    /**
     * {@inheritDoc}
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