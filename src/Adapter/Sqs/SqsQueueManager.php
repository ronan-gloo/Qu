<?php

namespace Qu\Adapter\Sqs;

use Aws\Sqs\SqsClient;
use Qu\Exception\InvalidArgumentException;
use Qu\Exception\OperationException;
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
        $config = new SqsQueueConfig();
        $config->setName($this->prepareQueueName($name));

        if ($this->has($name)) {
            $attributes = $this->readQueueAttributes($config->getName());
            $config->hydrate($attributes);
            return new SqsQueue($this->client, $config);
        }
        else {
            if (! $this->config->getCreateNotFound()) {
                throw new QueueNotFoundException('cannot find the queue with name ' . $name);
            }
            $config->setName($name);

            return $this->create($config);
        }
    }


    /**
     * Check if the name exists in the set of urls
     *
     * @param $name
     * @return boolean
     */
    public function has($name)
    {
        $name = $this->prepareQueueName($name);
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
        // SQS is resilient when creating a new queue, only if attributes are similar
        try {
            $this->client->createQueue([
                'QueueName'  => $this->prepareQueueName($options->getName()),
                'Attributes' => $options->toAttributes()
            ]);
        }
        catch (\Exception $e) {
            throw new RuntimeException('Cannot create the queue with name ' . $options->getName(), $e->getCode(), $e);
        }
        $options->setAccountId($this->config->getAccountId());
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
     * @param $name
     * @return string
     */
    protected function prepareQueueName($name)
    {
        return $this->config->getQueueNamePrefix() . strval($name);
    }

    /**
     * @param string $name
     * @throws OperationException
     * @return array
     */
    protected function readQueueAttributes($name)
    {
        try {
            // grab url before in order to read attributes
            $queueUrl = $this->client->getQueueUrl([
                'QueueName' => $name
            ]);
            $attributes = $this->client->getQueueAttributes([
                'QueueUrl' => $queueUrl->get('QueueUrl'),
                'AttributeNames' => ['All']
            ]);
            return $attributes->get('Attributes');
        }
        catch (\Exception $e) {
            throw new OperationException(sprintf(
                'Cannot read attributes for queue "%s":%s', $name, $e->getMessage()
            ), $e->getCode(), $e);
        }
    }
}