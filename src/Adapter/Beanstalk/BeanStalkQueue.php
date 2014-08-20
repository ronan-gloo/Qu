<?php

namespace Qu\Adapter\Beanstalk;

use Pheanstalk\Job;
use Pheanstalk\PheanstalkInterface;
use Qu\Exception\Exception;
use Qu\Iterator\QueueIteratorAwareTrait;
use Qu\Message\MessageAggregateInterface;
use Qu\Message\MessageInterface;
use Qu\Queue\QueueInterface;
use Qu\Encoder\EncoderAwareInterface;
use Qu\Encoder\EncoderAwareTrait;

class BeanStalkQueue implements QueueInterface, EncoderAwareInterface
{
    use QueueIteratorAwareTrait, EncoderAwareTrait;

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
     * {@inheritDoc}
     */
    public function enqueue(MessageInterface $message)
    {
        if ($message instanceof MessageAggregateInterface) {
            $this->enqueueAll($message->getMessages());
            return;
        }

        $jobId = $this->client->putInTube(
            $this->config->getTube(),
            $this->getEncoder()->encode($message),
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
     * {@inheritDoc}
     */
    public function dequeue()
    {
        $msg = null;
        $job = $this->client->reserveFromTube(
            $this->config->getTube(),
            $this->config->getWaitTimeout()
        );

        if ($job instanceof Job) {
            $msg = $this->getEncoder()->decode($job->getData());
            $msg->setId($job->getId());
        }

        return $msg;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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