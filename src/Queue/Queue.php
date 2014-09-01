<?php

namespace Qu\Queue;

use Qu\Exception\InvalidArgumentException;
use Qu\Exception\UnexpectedValueException;
use Qu\Message\Message;
use Qu\Message\MessageCollectionInterface;
use Qu\Message\MessageInterface;
use Traversable;

class Queue implements QueueInterface
{
    /**
     * @var QueueAdapterInterface
     */
    protected $queue;

    /**
     * @param QueueAdapterInterface $queue
     * @return self
     */
    public function setAdapter(QueueAdapterInterface $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return QueueAdapterInterface
     */
    public function getAdapter()
    {
        return $this->queue;
    }

    /**
     * @param $message
     * @throws \Qu\Exception\InvalidArgumentException
     */
    public function enqueue($message)
    {
        if ($message instanceof MessageCollectionInterface) {
            $this->getAdapter()->enqueueAll($message);
            return;
        }

        if ($message instanceof MessageInterface) {
            $this->getAdapter()->enqueue($message);
            return;
        }

        if (is_array($message)) {
            $this->getAdapter()->enqueue(new Message($message));
            return;
        }

        throw new InvalidArgumentException;
    }

    /**
     * @return MessageInterface
     * @throws \Qu\Exception\UnexpectedValueException
     */
    public function dequeue()
    {
        $message = $this->getAdapter()->dequeue();
        if ($message instanceof MessageInterface) {
            return $message;
        }

        throw new UnexpectedValueException(sprintf(
            'Invalid data returned by adapter.' .
            'Expecting an instance of Qu\Message\MessageInterface, got '
            ), is_object($message) ? get_class($message) : gettype($message)
        );
    }

    /**
     * @param MessageInterface $message
     * @throws \Qu\Exception\InvalidArgumentException
     * @return \Qu\Message\MessageInterface|null
     */
    public function requeue($message)
    {
        if ($message instanceof MessageInterface) {
            return $this->getAdapter()->requeue($message);
        }

        if ($message instanceof MessageCollectionInterface) {
            return $this->getAdapter()->requeueAll($message);
        }

        throw new InvalidArgumentException;
    }

    /**
     * @return \Qu\Iterator\QueueIteratorInterface|Traversable
     */
    public function getIterator()
    {
        return $this->getAdapter()->getIterator();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->getAdapter()->count();
    }
}