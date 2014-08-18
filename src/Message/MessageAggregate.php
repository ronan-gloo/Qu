<?php

namespace Qu\Message;

/**
 * Messages wrapper for batch insertions / deletions.
 * Only use it for insert and deletion, or you may face unexpected behaviors
 */
class MessageAggregate implements MessageAggregateInterface
{
    use MessageImplementTrait;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @param array $messages
     */
    public function __construct(array $messages = [])
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**w
     * @param MessageInterface $message
     * @return $this
     */
    public function addMessage(MessageInterface $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return MessageInterface[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}