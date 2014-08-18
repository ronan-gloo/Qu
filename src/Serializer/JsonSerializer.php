<?php

namespace Qu\Serializer;

use Qu\Message\MessageInterface;

class JsonSerializer implements SerializerInterface
{
    /**
     * @var array
     */
    protected $encodeOptions = [];

    /**
     * @var array
     */
    protected $decodeOptions = [JSON_OBJECT_AS_ARRAY];

    /**
     * @param array $decodeOptions
     * @return self
     */
    public function setDecodeOptions(array $decodeOptions)
    {
        $this->decodeOptions = array_values($decodeOptions);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecodeOptions()
    {
        return $this->decodeOptions;
    }

    /**
     * @param array $encodeOptions
     * @return self
     */
    public function setEncodeOptions(array $encodeOptions)
    {
        $this->encodeOptions = array_values($encodeOptions);
        return $this;
    }

    /**
     * @return array
     */
    public function getEncodeOptions()
    {
        return $this->encodeOptions;
    }

    /**
     * @param \Qu\Message\MessageInterface $message
     * @return string
     */
    public function serialize(MessageInterface $message)
    {
        $data = [
            'name' => get_class($message),
            'meta' => $message->getMeta(),
            'data' => $message->getData()
        ];

        $arguments = $this->encodeOptions;
        array_unshift($arguments, $data);

        return call_user_func_array('json_encode', $arguments);
    }

    /**
     * @param string $data
     * @throws \RuntimeException
     * @return mixed
     */
    public function unserialize($data)
    {
        $arguments = $this->decodeOptions;
        array_unshift($arguments, $data);

        $data = call_user_func_array('json_decode', $arguments);

        if (! isset($data['name']) || ! class_exists($data['name'])) {
            return null;
        }

        /** @var MessageInterface $message */
        $message = new $data['name'];

        if (isset($data['meta'])) {
            $message->setMeta($data['meta']);
        }
        if (isset($data['data'])) {
            $message->setData($data['data']);
        }

        return $message;
    }
}