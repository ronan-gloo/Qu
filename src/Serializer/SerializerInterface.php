<?php

namespace Qu\Serializer;

use Qu\Message\MessageInterface;

interface SerializerInterface
{
    /**
     * @param MessageInterface $message
     * @return mixed
     */
    public function serialize(MessageInterface $message);

    /**
     * @param mixed $string
     * @return MessageInterface
     */
    public function unserialize($string);
}