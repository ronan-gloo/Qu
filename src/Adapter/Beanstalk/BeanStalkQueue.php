<?php

namespace Qu\Adapter\Beanstalk;

use Pheanstalk\Job;
use Pheanstalk\PheanstalkInterface;
use Qu\Exception\Exception;
use Qu\Iterator\QueueIteratorAwareTrait;
use Qu\Message\MessageAggregateInterface;
use Qu\Message\MessageInterface;
use Qu\Queue\QueueInterface;
use Qu\Serializer\SerializerAwareInterface;
use Qu\Serializer\SerializerAwareTrait;

class BeanStalkQueue implements QueueInterface, SerializerAwareInterface
{
    use QueueIteratorAwareTrait, SerializerAwareTrait;

    /**
     * @var PheanstalkInterface;
     */
    protected $client;

    /**
     * @var BeanStalkQueueConfig
     */
    protected $config;

    /**
     * @param PheanstalkInterface $client
     * @param array $config
     */
    public function __construct(PheanstalkInterface $client, $config = [])
    {
        $this->client = $client;
        $this->config = $config instanceof BeanStalkQueueConfig ? $config : new BeanStalkQueueConfig($config);
        $client->watch($this->config->getTube());
    }

    /**
     * Insert a new message at the top of the queue
     *
     * @param MessageInterface $message
     * @throws \Qu\Exception\Exception
     * @return void
     */
    public function enqueue(MessageInterface $message)
    {
        if ($message instanceof MessageAggregateInterface) {
            $this->enqueueAll($message->getMessages());
            return;
        }

        $jobId = $this->client->putInTube(
            $this->config->getTube(),
            $this->getSerializer()->serialize($message),
            $message->getPriority() ?: $this->config->getPriority(),
            $message->getDelay() !== null ? $message->getDelay() : $this->config->getDelay(),
            $this->config->getTimeToRun()
        );

        if (! $jobId) {
            throw new Exception('Job queueing failed');
        }

        $message->setId($jobId);
    }

    /**
     * @param $messages
     */
    public function enqueueAll($messages)
    {
        foreach ($messages as $message) {
            $this->enqueue($message);
        }
    }

    /**
     * Extract the message from the queue.
     * Note that the message must be permanently removed from the queue
     *
     * @return MessageInterface
     */
    public function dequeue()
    {
        $msg = null;
        $job = $this->client->reserveFromTube(
            $this->config->getTube(),
            $this->config->getWaitTimeout()
        );

        if ($job instanceof Job) {
            $msg = $this->getSerializer()->unserialize($job->getData());
            $msg->setId($job->getId());
        }

        return $msg;
    }

    /**
     * As the enqueue method, requeue will add the given message at the to of the queue.
     * Requeueing message offer the opportunity to set an optional treatment for the given message.
     *
     * @param MessageInterface $message
     * @return void
     */
    public function requeue(MessageInterface $message)
    {
        if ($message instanceof MessageAggregateInterface) {
            $this->requeueAll($message->getMessages());
            return;
        }

        try {
            $this->client->release($message,
                $message->getPriority() !== null ? $message->getPriority() : $this->config->getPriority(),
                $message->getDelay()    !== null ? $message->getDelay()    : $this->config->getDelay()
            );
        }
        catch (\Exception $e) {}
    }

    /**
     * @param array|\Traversable $messages
     * @return void
     */
    public function requeueAll($messages)
    {
        foreach ($messages as $message) {
            $this->requeue($message);
        }
    }

    /**
     * Delete a particular message
     *
     * @param MessageInterface $message
     * @return void
     */
    public function remove(MessageInterface $message)
    {
        if ($message instanceof MessageAggregateInterface) {
            $this->deleteAll($message->getMessages());
            return;
        }
        try {
            $this->client->delete($message);
        }
        catch (\Exception $e) {}
    }

    /**
     * @param array|\Traversable $messages
     * @return void
     */
    public function deleteAll($messages)
    {
        foreach ($messages as $message) {
            $this->remove($message);
        }
    }

    /**
     * Count available jobs in queue
     *
     * @return int
     */
    public function count()
    {
        $tube     = $this->config->getTube();
        $response = $this->client->statsTube($tube);
        return (int) $response->getArrayCopy()['current-jobs-ready'];
    }

    /**
     * @return PheanstalkInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getTube()
    {
        return $this->config->getTube();
    }

    /**
     * @return BeanStalkQueueConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}