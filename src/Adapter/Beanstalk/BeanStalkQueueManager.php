<?php

namespace Qu\Adapter\Beanstalk;

use Pheanstalk\PheanstalkInterface;
use Qu\Exception\InvalidArgumentException;
use Qu\Exception\QueueNotFoundException;
use Qu\Exception\RuntimeException;
use Qu\Exception\UnsupportedFeatureException;
use Qu\Queue\QueueInterface;
use Qu\Queue\QueueManagerInterface;

class BeanStalkQueueManager implements QueueManagerInterface
{
    /**
     * @var PheanstalkInterface
     */
    protected $client;

    /**
     * @var BeanStalkQueueManagerConfig
     */
    protected $config;

    /**
     * @param PheanstalkInterface $client
     * @param array $config
     */
    public function __construct(PheanstalkInterface $client, $config = [])
    {
        $this->client = $client;
        $this->config = ! $config instanceof BeanStalkQueueManagerConfig
            ? new BeanStalkQueueManagerConfig($config)
            : $config
        ;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return in_array($name, $this->client->listTubes());
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        $name = $this->config->getTubeNamePrefix() . $name;
        if (! $this->has($name) && ! $this->config->getCreateNotFound()) {
            throw new QueueNotFoundException('Cannot find tube with name ' . $name);
        }

        return new BeanStalkQueue($this->client, compact('tube'));
    }

    /**
     * {@inheritDoc}
     */
    public function update(QueueInterface $queue, $data = null)
    {
        throw new UnsupportedFeatureException('Tubes are not updatable');
    }

    /**
     * {@inheritDoc}
     */
    public function flush(QueueInterface $queue)
    {
        if (! $queue instanceof BeanStalkQueue) {
            throw new RuntimeException(sprintf(
                'Expecting an instance of BeanStalkQueue, "%s" given', get_class($queue)
            ));
        }

        if (! $this->exist($queue)) {
            throw new QueueNotFoundException(sprintf('Queue with tube "%s" not found', $queue->getTube()));
        }

        // Clear jobs from the given queue
        $tube = $queue->getTube();
        foreach (['buried', 'delayed', 'ready'] as $state) {
            $this->peekDelete($tube, $state);
        }
    }

    /**
     * Beanstalk implicitly creates new tubes, so we just have to return the queue
     *
     * {@inheritDoc}
     */
    public function create($options)
    {
        $config = null;

        if (is_string($options)) {
            $config['tube'] = $options;
        }
        if (is_array($options)) {
            $config = new BeanStalkQueueConfig($options);
        }
        if (! $config instanceof BeanStalkQueueConfig) {
            throw new InvalidArgumentException('cannot create the queue with the given $options');
        }
        if (! $config->getTube()) {
            throw new RuntimeException('No tube given');
        }

        // inject prefixed name
        if (strpos($config->getTube(), $this->config->getTubeNamePrefix()) !== 0) {
            $config->setTube($this->config->getTubeNamePrefix() . $config->getTube());
        }

        return new BeanStalkQueue($this->client, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function remove(QueueInterface $queue)
    {
        if (! $queue instanceof BeanStalkQueue) {
            throw new InvalidArgumentException('Expecting a beanstak queue instance, got ' . get_class($queue));
        }

        $this->flush($queue);
        $this->client->ignore($queue->getTube());
    }

    /**
     * @param BeanStalkQueue $queue
     * @return bool
     */
    protected function exist(BeanStalkQueue $queue)
    {
        return in_array($queue->getTube(), $this->client->listTubes());
    }

    /**
     * @param $tube
     * @param $state
     * @return \Exception|null
     */
    protected function peekDelete($tube, $state)
    {
        try {
            while($job = $this->client->{'peek' . $state}($tube)) {
                $this->client->delete($job);
            }
        }
        catch (\Exception $e) {
            return $e;
        }
    }
}