<?php

namespace Qu\Serializer;

use Qu\Message\MessageInterface;

class MessageSerializer implements SerializerInterface
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer ?: new JsonSerializer();
    }

    /**
     * @param MessageInterface $message
     * @return string
     */
    public function serialize(MessageInterface $message)
    {

    }

    /**
     * @param string $string
     * @return MessageInterface
     */
    public function unserialize($string)
    {
        // TODO: Implement unserialize() method.
    }
} 