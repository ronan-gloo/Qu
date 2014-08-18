<?php

namespace Qu\Service;

use Qu\Message\MessageInterface;
use Qu\Queue\QueueInterface;
use Traversable;

/**
 * add Extra functionnalities to queues
 */
class QueueService implements QueueInterface
{
    protected $queue;

    /**
     * @param QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->queue->getIterator();
    }

    /**
     * Retrieve the last message from the queue
     *
     * @return MessageInterface|null
     */
    public function top()
    {
        return $this->queue->top();
    }

    /**
     * Insert a new message at the top of the queue
     *
     * @param MessageInterface $message
     * @return void
     */
    public function enqueue(MessageInterface $message)
    {
        $this->queue->enqueue($message);
    }

    /**
     * Extract the message from the queue.
     * Note that the message must be permanently removed from the queue
     *
     * @return MessageInterface
     */
    public function dequeue()
    {
        return $this->queue->dequeue();
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
        $this->queue->requeue($message);
    }

    /**
     * Delete a particular message
     *
     * @param MessageInterface $message
     * @return void
     */
    public function remove(MessageInterface $message)
    {
        $this->queue->remove($message);
    }

    /**
     * Return various information about the queue
     *
     * @return \ArrayObject
     */
    public function info()
    {
        return $this->queue->info();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->queue->count();
    }

    /**
     * loop through queue messages until $limit is reached
     *
     * @param integer $limit
     * @param callable $callable
     * @return $this
     */
    public function each($limit, callable $callable)
    {
        $count = 0;
        foreach ($this->queue as $message) {
            $count += 1;
            if (false === call_user_func($callable, $message, $this, $count) || $count >= $limit) {
                break;
            }
        }
        return $this;
    }
}