<?php

namespace Qu\Adapter\Sqs;

class SqsQueueConfig extends AbstractSqsConfig
{
    const DEFAULT_VISIBILITY_TIMEOUT = 30;      // seconds

    const MAX_BATCH_SIZE             = 10;      // items

    const MIN_DELAY_SECONDS          = 0;       // seconds
    const MAX_DELAY_SECONDS          = 900;     // seconds

    const MAX_MESSAGE_SIZE           = 262144;  // bytes
    const MIN_MESSAGE_SIZE           = 125;     // bytes

    const MAX_VISIBILITY_SECONDS     = 43200;   // seconds
    const MAX_POLLING_SECONDS        = 20;      // seconds

    const MAX_RETENTION_PERIOD       = 345600;  // seconds, 4 days
    const MIN_RETENTION_PERIOD       = 60;      // seconds

    /**
     * Properties blacklist when exporting attributes
     *
     * @var array
     */
    protected static $attributes = [
        'DelaySeconds',
        'VisibilityTimeout',
        'MaximumMessageSize',
        'ReceiveMessageWaitTimeSeconds',
        'MessageRetentionPeriod'
    ];

    /**
     * Defer, in seconds, the availability of the message in tth queue
     *
     * @var int
     */
    protected $delaySeconds = self::MIN_DELAY_SECONDS;

    /**
     * How long, in seconds, messages visibility will be locked
     *
     * @var int
     */
    protected $visibilityTimeout = self::DEFAULT_VISIBILITY_TIMEOUT;

    /**
     * Set the maximum size for messages, in kb
     *
     * @var null
     */
    protected $maximumMessageSize = self::MAX_MESSAGE_SIZE;

    /**
     * Time, in seconds, to wait for incoming messages
     *
     * @var int
     */
    protected $receiveMessageWaitTimeSeconds = self::MAX_POLLING_SECONDS;

    /**
     * How long messages must reside in queue in seconds
     *
     * @var int
     */
    protected $messageRetentionPeriod = self::MAX_RETENTION_PERIOD;

    /**
     * The number of message to treat per batch
     *
     * @var int
     */
    protected $batchSize = self::MAX_BATCH_SIZE;

    /**
     * Limited to alphanumeric (and - or _) 80 characters max
     *
     * @var string
     */
    protected $name = '';

    /**
     * convert config properties to Sqs attributes syntax.
     *
     * @return array
     */
    public function toAttributes()
    {
        $attributes = [];
        foreach (static::$attributes as $name) {
            $attributes[$name] = $this->{lcfirst($name)};
        }

        return $attributes;
    }

    /**
     * Value is constrain between 0 & MAX_DELAY_SECONDS
     *
     * @param int|float|string $messageDelay
     * @return self
     */
    public function setDelaySeconds($messageDelay)
    {
        $this->delaySeconds = min(
            max(0, (int) round($messageDelay)),
            static::MAX_DELAY_SECONDS
        );

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDelaySeconds()
    {
        return $this->delaySeconds;
    }

    /**
     * @param null $maximumMessageSize
     * @return self
     */
    public function setMaximumMessageSize($maximumMessageSize)
    {
        $this->maximumMessageSize = min(
            max(static::MIN_MESSAGE_SIZE, (int) round($maximumMessageSize)),
            static::MAX_MESSAGE_SIZE
        );

        return $this;
    }

    /**
     * @return null
     */
    public function getMaximumMessageSize()
    {
        return $this->maximumMessageSize;
    }

    /**
     * @param int $visibilityTimeout
     * @return self
     */
    public function setVisibilityTimeout($visibilityTimeout)
    {
        $this->visibilityTimeout = min(
            max(0, (int) round($visibilityTimeout)),
            static::MAX_VISIBILITY_SECONDS
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getVisibilityTimeout()
    {
        return $this->visibilityTimeout;
    }

    /**
     * @param int $waitTimeSeconds
     * @return self
     */
    public function setReceiveMessageWaitTimeSeconds($waitTimeSeconds)
    {
        $this->receiveMessageWaitTimeSeconds = min(
            max(0, (int) round($waitTimeSeconds)),
            static::MAX_POLLING_SECONDS
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getReceiveMessageWaitTimeSeconds()
    {
        return $this->receiveMessageWaitTimeSeconds;
    }

    /**
     * @param int $messageRetentionPeriod
     * @return self
     */
    public function setMessageRetentionPeriod($messageRetentionPeriod)
    {
        $this->messageRetentionPeriod = min(
            max(static::MIN_RETENTION_PERIOD, (int) round($messageRetentionPeriod)),
            static::MAX_RETENTION_PERIOD
        );
        return $this;
    }

    /**
     * @return int
     */
    public function getMessageRetentionPeriod()
    {
        return $this->messageRetentionPeriod;
    }

    /**
     * @param string $name
     * @throws \Qu\Exception\InvalidArgumentException
     * @return self
     */
    public function setName($name)
    {
        $name = trim($name);
        $this->validateQueueName($name);
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $batchSize
     * @return self
     */
    public function setBatchSize($batchSize)
    {
        $this->batchSize = min(max(1, (int) round($batchSize)), static::MAX_BATCH_SIZE);

        return $this;
    }

    /**
     * @return int
     */
    public function getBatchSize()
    {
        return $this->batchSize;
    }
}