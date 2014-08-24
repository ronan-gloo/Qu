<?php

namespace Qu\Iterator;

use Qu\Queue\QueueAdapterInterface;

interface QueueIteratorInterface extends \Iterator
{
    /**
     * @param QueueInterface $queue
     * @internal param int $mode
     */
    public function __construct(QueueAdapterInterface $queue);
}