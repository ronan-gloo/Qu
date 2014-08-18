<?php

namespace Qu\Adapter\Sqs;

use Aws\Sqs\SqsClient;
use Guzzle\Service\Resource\Model;
use Qu\Iterator\QueueIteratorAwareTrait;
use Qu\Message\MessageAggregate;
use Qu\Message\MessageInterface;
use Qu\Queue\QueueInterface;
use Qu\Serializer\SerializerAwareInterface;
use Qu\Serializer\SerializerAwareTrait;

class SqsQueue implements QueueInterface, SerializerAwareInterface
{
    use SerializerAwareTrait, QueueIteratorAwareTrait;

    const RECEIPT_HANDLE_KEY = 'sqs-receipt-handle';
    const BATCH_MAX_SIZE     = 10;

    /**
     * @var SqsClient
     */
    protected $client;

    /**
     * @var SqsQueueConfig
     */
    protected $config;

    /**
     * Number of items per batch request
     *
     * @var int
     */
    protected $batchSize = self::BATCH_MAX_SIZE;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param SqsClient $client
     * @param array|\Qu\Adapter\Sqs\SqsQueueConfig $options
     */
    public function __construct(SqsClient $client, $options)
    {
        $this->client = $client;
        $this->config = $options instanceof SqsQueueConfig ? $options : new SqsQueueConfig($options);
        $this->url    = $client->getBaseUrl() . '/' . $this->config->getAccountId() . '/' . $this->config->getName();
    }

    /**
     * Insert a new message at the top of the queue
     *
     * @param MessageInterface $message
     * @return void
     */
    public function enqueue(MessageInterface $message)
    {
        $messages = $message instanceof MessageAggregate ? $message->getMessages() : [$message];
        if (! $messages) {
            return;
        }

        $serializer = $this->getSerializer();
        $config = $this->getConfig();

        $request = [
            'QueueUrl' => $this->getUrl(),
            'Entries'  => []
        ];

        foreach (array_chunk($messages, $this->batchSize) as $chunk) {
            foreach ($chunk as $id => $message) {
                $request['Entries'][$id] = [
                    'Id'           => $id,
                    'DelaySeconds' => $message->getMeta('delay') ?: $config->getDelaySeconds(),
                    'QueueUrl'     => $this->getUrl(),
                    'MessageBody'  => $serializer->serialize($message)
                ];
            }

            $items = $this->client->sendMessageBatch($request)->getPath('Successful') ?: [];

            foreach ($items as $item) {
                $messages[$item['Id']]->setId($item['MessageId']);
            }
        }
    }

    /**
     * Extract the message from the queue.
     * Note that the message must be permanently removed from the queue
     *
     * @return MessageInterface|null
     */
    public function dequeue()
    {
        $message = null;
        $options = $this->config;

        $response = $this->client->receiveMessage([
            'QueueUrl'        => $this->getUrl(),
            'WaitTimeSeconds' => $options->getReceiveMessageWaitTimeSeconds()
        ]);

        if ($response instanceof Model) {
            $data = $response->getPath('Messages/0');
            if ($data) {
                $message = $this->getSerializer()->unserialize($data['Body']);
                $message->setId($data['MessageId']);
                $message->setMeta(static::RECEIPT_HANDLE_KEY, $data['ReceiptHandle']);
            }
        }

        return $message;
    }

    /**
     * @param MessageInterface $message
     * @return void
     */
    public function remove(MessageInterface $message)
    {
        $messages = $message instanceof MessageAggregate ? $message->getMessages() : [$message];
        if (! $messages) {
            return;
        }

        $request = [
            'QueueUrl' => $this->getUrl(),
            'Entries'  => []
        ];

        foreach (array_chunk($messages, $this->batchSize) as $chunk) {
            foreach ($chunk as $Id => $msg) {
                $ReceiptHandle = $message->getMeta(static::RECEIPT_HANDLE_KEY);
                if ($ReceiptHandle) {
                    $request['Entries'][] = compact('Id', 'ReceiptHandle');
                }
            }

            if ($request['Entries']) {
                $this->client->deleteMessageBatch($request);
            }
        }
    }

    /**
     * As the enqueue method, requeue will add the given message at the to of the queue.
     * Requeing message offer the opportunity to set an optional treatment for the given message.
     *
     * @param MessageInterface $message
     * @throws \InvalidArgumentException
     * @return void
     */
    public function requeue(MessageInterface $message)
    {
        $receiptHandle = $message->getMeta(static::RECEIPT_HANDLE_KEY);

        if (! $message->getId() || ! $receiptHandle) {
            throw new \InvalidArgumentException('Message as not been in queue previously');
        }

        $this->enqueue($message);
    }

    /**
     * @return int
     */
    public function count()
    {
        $response = $this->client->getQueueAttributes([
            'QueueUrl'       => $this->getUrl(),
            'AttributeNames' => ['All']
        ]);

        return (int) $response->getPath('Attributes/ApproximateNumberOfMessages');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param \Qu\Adapter\Sqs\SqsQueue|string $queue
     * @return self
     */
    public function exchangeUrl(SqsQueue $queue)
    {
        $this->url = $queue->url;

        return $this;
    }

    /**
     * @return \Qu\Adapter\Sqs\SqsQueueConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}