<?php

namespace Qu\Message;

use Traversable;

/**
 * Messages wrapper for batch insertions / deletions.
 * Only use it for insert and deletion, or you may face unexpected behaviors
 */
class MessageCollection implements MessageCollectionInterface, \IteratorAggregate, \Countable
{
    /**
     * @var int
     */
    protected $delay;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @param array $messages
     * @throws \Qu\Exception\InvalidArgumentException
     */
    public function __construct(array $messages = [])
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * @param int $delay
     * @return self
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritDoc}
     */
    public function addMessage(MessageInterface $message)
    {
        // set global delay if not set in message
        if (null !== $this->getDelay() && null === $message->getDelay()) {
            $message->setDelay($this->getDelay());
        }

        // set global priority if not set in message
        if (null !== $this->getPriority() && null === $message->getPriority()) {
            $message->setPriority($this->getPriority());
        }

        $this->messages[] = $message;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->messages);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->messages);
    }
}