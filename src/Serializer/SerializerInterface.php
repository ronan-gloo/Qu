<?php

namespace Qu\Serializer;

use Qu\Message\MessageInterface;

interface SerializerInterface
{
    /**
     * @param MessageInterface $message
     * @return string
     */
    public function serialize(MessageInterface $message);

    /**
     * @param string $string
     * @return MessageInterface
     */
    public function unserialize($string);
}