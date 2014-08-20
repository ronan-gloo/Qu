<?php

namespace Qu\Encoder;

use Qu\Message\MessageInterface;

interface EncoderInterface
{
    /**
     * @param MessageInterface $message
     * @return mixed
     */
    public function encode(MessageInterface $message);

    /**
     * @param mixed $string
     * @return MessageInterface
     */
    public function decode($string);
}