<?php

namespace Qu\Queue;

use Qu\Exception\QueueNotFoundException;
use Qu\Exception\RuntimeException;

interface QueueManagerInterface
{
    /**
     * Get the queue based on its definitions.
     * Implementations are free to create and return a non existing queue
     * when invoking the get method. Be sur to sync all queue specifications
     * according $options parameters.
     *
     * @param mixed $options
     * @return QueueInterface
     * @throws QueueNotFoundException   If the queue does not exists
     * @throws RuntimeException         Otherwise
     */
    public function get($options);

    /**
     * Create a queue from given options
     *
     * @param $options
     * @return self
     */
    public function create($options);

    /**
     * Update the queue
     *
     * @param QueueInterface $queue
     * @return void
     * @throws QueueNotFoundException   If the queue cannot be found
     * @throws RuntimeException         Otherwise
     */
    public function update(QueueAdapterInterface $queue);

    /**
     * Permanently delete a queue.
     *
     * @param QueueInterface $queue
     * @return void
     * @throws QueueNotFoundException   If the queue cannot be found
     * @throws RuntimeException         Otherwise
     */
    public function remove(QueueAdapterInterface $queue);

    /**
     * Remove all available from the queue.
     * implementations will assume that busy|invisible elements mst not be removed
     *
     * @param QueueInterface $queue
     * @return void
     * @throws QueueNotFoundException   If the queue cannot be found
     * @throws RuntimeException         Otherwise
     */
    public function flush(QueueAdapterInterface $queue);
}