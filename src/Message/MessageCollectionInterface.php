<?php

namespace Qu\Message;

interface MessageCollectionInterface
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