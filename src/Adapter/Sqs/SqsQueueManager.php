<?php

namespace Qu\Adapter\Sqs;

use Aws\Sqs\SqsClient;
use Qu\Exception\InvalidArgumentException;
use Qu\Exception\QueueNotFoundException;
use Qu\Exception\RuntimeException;
use Qu\Queue\QueueAdapterInterface;
use Qu\Queue\QueueManagerInterface;

class SqsQueueManager implements QueueManagerInterface
{
    /**
     * @var SqsClient
     */
    protected $client;

    /**
     * @var \ArrayObject
     */
    protected $config;

    /**
     * @param SqsClient $client
     * @param array|SqsQueue[]|SqsQueueManagerConfig $config
     */
    public function __construct(SqsClient $client, SqsQueueManagerConfig $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        $queue = new SqsQueue($this->client, [
            'account_id' => $this->config->getAccountId(),
            'name'       => $this->config->getQueueNamePrefix() . strval($name)
        ]);

        if (! $this->exists($queue)) {
            if (! $this->config->getCreateNotFound()) {
                throw new QueueNotFoundException('cannot find the queue with name ' . $name);
            }
            $this->client->createQueue([
                'QueueName'  => $queue->getConfig()->getName(),
                'Attributes' => $queue->getConfig()->toAttributes()
            ]);
        }

        return $queue;
    }

    /**
     * Check if the name exists in the set of urls
     *
     * @param $name
     * @return boolean
     */
    public function has($name)
    {
        $name = $this->config->getQueueNamePrefix() . $name;
        foreach ($this->getUrls() as $url) {
            if ($name === basename($url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function create($options)
    {
        if (! $options instanceof SqsQueueConfig) {
            throw new InvalidArgumentException('$options cannot be used to create a new queue');
        }

        $config     = $this->config;
        $name       = $options->getName();
        $prefix     = $config->getQueueNamePrefix();
        $QueueName  = strpos($prefix, $name) === 0 ? $name : $prefix . $name;

        // SQS is resilient when creating a new queue, only if attributes are similar
        try {
            $Attributes = $options->toAttributes();
            $this->client->createQueue(compact('QueueName', 'Attributes'));
        }
        catch (\Exception $e) {
            throw new RuntimeException('Cannot create the queue with name ' . $QueueName, $e->getCode(), $e);
        }

        return new SqsQueue($this->client, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function remove(QueueAdapterInterface $queue)
    {
        if (! $queue instanceof SqsQueue) {
            throw new InvalidArgumentException('expecting an instance of SqsQueue');
        }

        try {
            $this->client->deleteQueue(['QueueUrl' => $queue->getUrl()]);
        }
        catch (\Exception $ex) {
            throw new RuntimeException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function flush(QueueAdapterInterface $queue)
    {
        if (! $queue instanceof SqsQueue) {
            throw new InvalidArgumentException('expecting an instance of SqsQueue');
        }

        // we don't invoke count on each iteration, tis can cause flushing message
        // that are enqueued by other services during this operation
        $count = $queue->count();
        while($count--) {
            $message = $queue->dequeue();
            if ($message) {
                $queue->remove($message);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update(QueueAdapterInterface $queue)
    {
        if (! $queue instanceof SqsQueue) {
            throw new InvalidArgumentException('expecting an instance of SqsQueue');
        }

        // Update queue attributes if required
        $Attributes = $queue->getConfig()->toAttributes();
        $QueueUrl   = $queue->getUrl();
        $this->client->setQueueAttributes(compact('QueueUrl', 'Attributes'));
    }

    /**
     * @return array
     */
    protected function getUrls()
    {
        $elements = $this->client->listQueues(['QueueNamePrefix' => $this->config->getQueueNamePrefix()]);

        return $elements->getPath('QueueUrls') ?: [];
    }

    /**
     * @param SqsQueue $queue
     * @return bool
     */
    protected function exists(SqsQueue $queue)
    {
        return in_array($queue->getUrl(), $this->getUrls());
    }
}