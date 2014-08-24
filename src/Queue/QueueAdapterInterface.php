<?php

namespace Qu\Queue;

use Qu\Message\MessageCollectionInterface;
use Qu\Message\MessageInterface;

interface QueueAdapterInterface extends \IteratorAggregate, \Countable
{
    /**
     * Insert a new message at the top of the queue
     *
     * @param MessageInterface $message
     * @return void
     */
    public function enqueue(MessageInterface $message);

    /**
     * Message batch processing
     *
     * @param $messages
     * @return mixed
     */
    public function enqueueAll(MessageCollectionInterface $messages);

    /**
     * Extract the message from the queue.
     * Note that the message can be permanently removed from the queue
     *
     * @return MessageInterface
     */
    public function dequeue();

    /**
     * Requeueing message offer the opportunity to set an optional treatment for the given message.
     * Requeued message can acquire a new id if necessary.
     *
     * @param MessageInterface $message
     * @return MessageInterface|void        A message if the implementation requires to set a new ID
     */
    public function requeue(MessageInterface $message);

    /**
     * Message batch processing
     *
     * @param $messages
     * @return mixed
     */
    public function requeueAll(MessageCollectionInterface $messages);

    /**
     * Remove permanently a particular message from the queue
     *
     * @param MessageInterface $message
     * @return void
     */
    public function remove(MessageInterface $message);

    /**
     * Message batch processing
     *
     * @param $messages
     * @return mixed
     */
    public function removeAll(MessageCollectionInterface $messages);

    /**
     * Return the number of actual available items in queue.
     * Reserved / running and other locked items must be ignored
     *
     * @return int
     */
    public function count();

    /**
     * @return \Qu\Iterator\QueueIteratorInterface
     */
    public function getIterator();
}