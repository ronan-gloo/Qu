<?php

namespace Qu\Iterator;

use Qu\Queue\QueueInterface;

interface QueueIteratorInterface extends \Iterator
{
    /**
     * @param QueueInterface $queue
     * @internal param int $mode
     */
    public function __construct(QueueInterface $queue);
}