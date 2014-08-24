<?php

namespace Qu\Iterator;

use Qu\Queue\QueueAdapterInterface;

class QueueIterator implements QueueIteratorInterface
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @var \Qu\Message\MessageInterface
     */
    protected $current;

    /**
     * {@inheritDoc}
     */
    public function __construct(QueueAdapterInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->current = count($this->queue) ? $this->queue->dequeue() : null;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->current = count($this->queue) ? $this->queue->dequeue() : null;
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        if (null !== $this->current) {
            return $this->current->getId();
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return isset($this->current);
    }
}