<?php

namespace Qu\Queue;

interface QueueInterface extends \IteratorAggregate, \Countable
{
    public function enqueue($message);

    public function requeue($message);

    public function dequeue();

    public function remove($message);
}