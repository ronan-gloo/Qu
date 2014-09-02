<?php

namespace Qu\Adapter\Sqs;

use Qu\Config\HydratorAwareInterface;
use Qu\Config\HydratorAwareTrait;
use Qu\Exception\InvalidArgumentException;

class SqsQueueConfig implements HydratorAwareInterface
{
    use HydratorAwareTrait;

    const DEFAULT_DELAY_DELIVERY     = 0;      // seconds
    const DEFAULT_RETENTION_PERIOD   = 345600; // seconds, 4 days
    const DEFAULT_MAX_MESSAGE_SIZE   = 262144; // bytes
    const DEFAULT_VISIBILITY_TIMEOUT = 30;     // seconds
    const DEFAULT_POLLING_TIME       = 20;     // seconds
    const BATCH_MAX_SIZE             = 10;

    /**
     * Properties blacklist when exporting attributes
     *
     * @var array
     */
    private static $skippedAttributes = ['accountId', 'name', 'arrayHydrator', 'batchSize'];

    /**
     * Defer, in seconds, the availability of the message in tth queue
     *
     * @var int
     */
    protected $delaySeconds = self::DEFAULT_DELAY_DELIVERY;

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
    protected $maximumMessageSize = self::DEFAULT_MAX_MESSAGE_SIZE;

    /**
     * Time, in seconds, to wait for incoming messages
     *
     * @var int
     */
    protected $receiveMessageWaitTimeSeconds = self::DEFAULT_POLLING_TIME;

    /**
     * How long messages must reside in queue in seconds
     *
     * @var int
     */
    protected $messageRetentionPeriod = self::DEFAULT_RETENTION_PERIOD;

    /**
     * The number of message to treat per batch
     *
     * @var int
     */
    protected $batchSize = self::BATCH_MAX_SIZE;

    /**
     * @var int
     */
    protected $accountId;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        if ($options) {
            $this->hydrate($options);
        }
    }

    /**
     * convert config properties to Sqs attributes syntax
     */
    public function toAttributes()
    {
        $attributes = [];

        foreach ($this as $name => $property) {
            if (! in_array($name, self::$skippedAttributes)) {
                $attributes[ucfirst($name)] = $property;
            }
        }

        return $attributes;
    }

    /**
     * @param mixed $messageDelay
     * @return self
     */
    public function setDelaySeconds($messageDelay)
    {
        $this->delaySeconds = (int) $messageDelay;
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
        $this->maximumMessageSize = (int) $maximumMessageSize;
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
        $this->visibilityTimeout = (int) $visibilityTimeout;
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
     * @param int $WaitTimeSeconds
     * @return self
     */
    public function setReceiveMessageWaitTimeSeconds($WaitTimeSeconds)
    {
        $this->receiveMessageWaitTimeSeconds = (int) $WaitTimeSeconds;
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
        $this->messageRetentionPeriod = (int) $messageRetentionPeriod;
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
     * @param string $accountId
     * @return self
     */
    public function setAccountId($accountId)
    {
        $this->accountId = (int) $accountId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string) $name;
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
     * @throws \Qu\Exception\InvalidArgumentException
     * @return self
     */
    public function setBatchSize($batchSize)
    {
        $intValue = (int) $batchSize;

        if ($intValue > static::BATCH_MAX_SIZE) {
            throw new InvalidArgumentException(sprintf('Batch size cannot exceed %d items', static::BATCH_MAX_SIZE));
        }

        $this->batchSize = $batchSize;

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