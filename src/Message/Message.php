<?php

namespace Qu\Message;

/**
 * Generic message class implementation
 */
class Message implements MessageInterface
{
    use MessagePrototypeTrait;

    /**
     * @param array $metadata
     * @param array $body
     */
    public function __construct($body = [], array $metadata = [])
    {
        if ($body instanceof \Traversable) {
            $body = iterator_to_array($body);
        }

        if (! is_array($body)) {
            $body = compact('body');
        }

        $this->metadata = $metadata;
        $this->data = $body;
    }
}