<?php

namespace Qu\Iterator;

use Qu\Queue\QueueInterface;

class QueueIterator implements QueueIteratorInterface
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * Represents the current message
     * @var \Qu\Message\MessageInterface
     */
    protected $current;

    /**
     * @param \Qu\Queue\QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        $this->current = count($this->queue) ? $this->queue->dequeue() : null;
    }

    public function rewind()
    {
        $this->current = count($this->queue) ? $this->queue->dequeue() : null;
    }

    public function key()
    {
        if (null !== $this->current) {
            return $this->current->getId();
        }
        return null;
    }

    public function valid()
    {
        return isset($this->current);
    }
}