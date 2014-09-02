<?php

namespace Qu\Encoder;

use Qu\Message\MessageInterface;

class JsonEncoder implements EncoderInterface
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
     * @return selfw
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
     * {@inheritDoc}
     * @return string
     */
    public function encode(MessageInterface $message)
    {
        $data = [
            'name'     => get_class($message),
            'id'       => $message->getId(),
            'priority' => $message->getPriority(),
            'delay'    => $message->getDelay(),
            'metadata' => $message->getMetadata(),
            'data'     => $message->getData(),
        ];

        $arguments = $this->encodeOptions;
        array_unshift($arguments, $data);

        return call_user_func_array('json_encode', $arguments);
    }

    /**
     * {@inheritDoc}
     * @param string
     */
    public function decode($data)
    {
        $arguments = $this->decodeOptions;
        array_unshift($arguments, $data);

        $data = (array) call_user_func_array('json_decode', $arguments);
        if (! isset($data['name']) || ! class_exists($data['name'])) {
            return null;
        }

        /** @var MessageInterface $message */
        $message = new $data['name'];

        if (isset($data['id'])) {
            $message->setId($data['id']);
        }
        if (isset($data['delay'])) {
            $message->setDelay($data['delay']);
        }
        if (isset($data['priority'])) {
            $message->setPriority($data['priority']);
        }
        if (isset($data['metadata'])) {
            $message->setMetadata($data['metadata']);
        }
        if (isset($data['data'])) {
            $message->setData($data['data']);
        }

        return $message;
    }
}