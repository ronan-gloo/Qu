<?php

namespace Qu\Iterator;

trait QueueIteratorAwareTrait
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @param QueueIteratorInterface $iterator
     */
    public function setIterator(QueueIteratorInterface $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @return QueueIteratorInterface
     */
    public function getIterator()
    {
        if (null === $this->iterator) {
            $this->iterator = new QueueIterator($this);
        }
        return $this->iterator;
    }
}