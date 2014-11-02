<?php

namespace Qu\Adapter\Sqs;

use Qu\Config\HydratorAwareTrait;
use Qu\Exception\InvalidArgumentException;

abstract class AbstractSqsConfig
{
    use HydratorAwareTrait;

    const REGEX_QUEUE_NAME = '/^[A-z-0-9_-]{1,80}$/';
    const REGEX_ACCOUNT_ID = '/^\d{12}$/';

    /**
     * Required AWS account id
     * @var string|int
     */
    protected $accountId;

    /**
     * @param int $accountId
     * @return self
     */
    public function setAccountId($accountId)
    {
        $this->validateAccountId($accountId);
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

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
     * @param $accountId
     * @throws InvalidArgumentException
     */
    protected function validateAccountId($accountId)
    {
        if (! preg_match(static::REGEX_ACCOUNT_ID, $accountId)) {
            throw new InvalidArgumentException('The account id appears to be invalid. It must be a 12-digits number');
        }
    }

    /**
     * @param $name
     * @throws InvalidArgumentException
     */
    protected function validateQueueName($name)
    {
        if (! preg_match(static::REGEX_QUEUE_NAME, $name)) {
            throw new InvalidArgumentException(
                'Queue names must be 1-80 characters in length and be composed of alphanumeric characters, hyphens (-), and underscores (_)'
            );
        }
    }
} 