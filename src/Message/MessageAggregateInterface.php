<?php

namespace Qu\Message;

interface MessageAggregateInterface extends MessageInterface
{
    /**
     * @param MessageInterface $message
     * @return self
     */
    public function addMessage(MessageInterface $message);

    /**
     * @return MessageInterface[]
     */
    public function getMessages();
} 