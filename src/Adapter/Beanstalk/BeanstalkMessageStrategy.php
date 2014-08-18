<?php

namespace Qu\Adapter\Beanstalk;

use Qu\Message\MessageInterface;
use Qu\Message\MessageOperationStrategyInterface;

class BeanstalkMessageStrategy implements MessageOperationStrategyInterface
{
    protected $queue;

    public function __construct(BeanStalkQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Can be invoked When the message has been correctly treated
     *
     * @param MessageInterface $message
     * @return void
     */
    public function succeed(MessageInterface $message)
    {
        $this->queue->remove($message);
    }

    /**
     * Can be invoked when an error occur in message treatment,
     * but not critical. This is a good place to re-schedule message.
     *
     * @param MessageInterface $message
     * @return void
     */
    public function recoverable(MessageInterface $message)
    {
        $this->queue->requeue($message);
    }

    /**
     * The message treatment failed
     *
     * @param MessageInterface $message
     * @return void
     */
    public function failed(MessageInterface $message)
    {
        $this->queue->remove($message);
    }
} 