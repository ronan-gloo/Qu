<?php

namespace Qu\Adapter\ZendJobQueue;

use Qu\Message\MessageInterface;
use Qu\Serializer\SerializerInterface;

class ZendJobSerializer implements SerializerInterface
{
    const CALLBACK_URL_META = 'zjq-callback-url';

    /**
     * @param MessageInterface $message
     * @param ZendQueueConfig $config
     * @return string
     */
    public function serialize(MessageInterface $message, ZendQueueConfig $config = null)
    {
        $timeString   = $message->getDelay() ? $message->getDelay() : $config->getScheduleDelay();
        $scheduleDate = date_create(sprintf('+ %d seconds', intval($timeString)));

        return [
            $message->getMeta(static::CALLBACK_URL_META) ?: $config->getCallbackUrl(),
            [
                'name' => get_class($message),
                'meta' => $message->getMeta(),
                'data' => $message->getData()
            ],
            [
                'priority'      => $message->getPriority() === null ? $config->getPriority() : $message->getPriority(),
                'schedule_time' => $scheduleDate->format('Y-m-d H:i:s'),
                'queue_name'    => $config->getName()
            ]
        ];
    }

    /**
     * @param string $data
     * @internal param string $string
     * @return MessageInterface
     */
    public function unserialize($data)
    {
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