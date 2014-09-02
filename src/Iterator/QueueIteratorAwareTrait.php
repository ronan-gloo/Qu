<?php

namespace Qu\Iterator;

use Qu\Queue\QueueInterface;

trait QueueIteratorAwareTrait
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @param QueueIteratorInterface $iterator
     * @return $this
     */
    public function setIterator(QueueIteratorInterface $iterator)
    {
        $this->iterator = $iterator;

        return $this;
    }

    /**
     * @return QueueIteratorInterface
     */
    public function getIterator()
    {
        if (null === $this->iterator && $this instanceof QueueInterface) {
            $this->iterator = new QueueIterator($this);
        }
        return $this->iterator;
    }
}