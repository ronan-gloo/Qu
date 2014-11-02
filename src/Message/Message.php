<?php

namespace Qu\Message;
use Qu\Exception\InvalidArgumentException;

/**
 * Generic message class implementation
 */
class Message implements MessageInterface
{
    use MessagePrototypeTrait;

    /**
     * @param array $body
     * @param array $metadata
     * @throws InvalidArgumentException
     */
    public function __construct($body = [], $metadata = [])
    {
        if ($metadata instanceof \Traversable) {
            $metadata = iterator_to_array($metadata);
        }
        if (! is_array($metadata)) {
            throw new InvalidArgumentException('Metadata must be a type of \Traversable, or an array');
        }
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