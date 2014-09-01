<?php

namespace Qu\Adapter\Sqs;

use Aws\Sqs\SqsClient;
use Guzzle\Service\Resource\Model;
use Qu\Exception\OperationException;
use Qu\Iterator\QueueIteratorAwareTrait;
use Qu\Message\MessageCollectionInterface;
use Qu\Message\MessageInterface;
use Qu\Queue\QueueAdapterInterface;
use Qu\Encoder\EncoderAwareInterface;
use Qu\Encoder\EncoderAwareTrait;

class SqsQueue implements QueueAdapterInterface, EncoderAwareInterface
{
    use EncoderAwareTrait, QueueIteratorAwareTrait;

    const RECEIPT_HANDLE_KEY = 'sqs-receipt-handle';

    /**
     * @var SqsClient
     */
    protected $client;

    /**
     * @var SqsQueueConfig
     */
    protected $config;

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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param SqsQueue $queue
     * @return $this
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

    /**
     * {@inheritDoc}
     */
    public function enqueue(MessageInterface $message)
    {
        $result = $this->client->sendMessage([
            'QueueUrl'     => $this->getUrl(),
            'MessageBody'  => $this->getEncoder()->encode($message),
            'DelaySeconds' => $this->getMessageDelay($message)
        ]);

        if ($result instanceof Model) {
            $message->setId($result->get('MessageId'));
        }
    }

    /**
     * Message batch processing
     *
     * @param $messages
     * @return mixed
     */
    public function enqueueAll(MessageCollectionInterface $messages)
    {
        $serializer = $this->getEncoder();
        $request = [
            'QueueUrl' => $this->getUrl(),
            'Entries'  => []
        ];

        $messages = $messages->getMessages();
        foreach (array_chunk($messages, $this->config->getBatchSize()) as $chunk) {
            foreach ($chunk as $id => $message) {
                $request['Entries'][$id] = [
                    'Id'           => $id,
                    'DelaySeconds' => $this->getMessageDelay($message),
                    'QueueUrl'     => $this->getUrl(),
                    'MessageBody'  => $serializer->encode($message)
                ];
            }

            $result = $this->client->sendMessageBatch($request);
            $items  = $result->getPath('Successful') ?: [];
            foreach ($items as $item) {
                $messages[$item['Id']]->setId($item['MessageId']);
            }
        }
    }


    /**
     * {@inheritDoc}
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
                $message = $this->getEncoder()->decode($data['Body']);
                $message->setId($data['MessageId']);
                $message->setMetadata(static::RECEIPT_HANDLE_KEY, $data['ReceiptHandle']);
            }
        }

        return $message;
    }

    /**
     * Remove permanently a particular message from the queue
     *
     * @param MessageInterface $message
     * @throws \Qu\Exception\OperationException
     * @return void
     */
    public function remove(MessageInterface $message)
    {
        try {
            $this->client->deleteMessage([
                'QueueUrl'      => $this->getUrl(),
                'ReceiptHandle' => $message->getMetadata(static::RECEIPT_HANDLE_KEY),
            ]);
        }
        catch (\Exception $e) {
            throw new OperationException($e->getMessage(), 0, $e);
        }

        $message->setId(null);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAll(MessageCollectionInterface $messages)
    {
        $request = [
            'QueueUrl' => $this->getUrl(),
            'Entries'  => []
        ];

        $messages = $messages->getMessages();
        foreach (array_chunk($messages, $this->config->getBatchSize()) as $chunk) {
            foreach ($chunk as $Id => $message) {
                $ReceiptHandle = $message->getMeta(static::RECEIPT_HANDLE_KEY);
                if ($ReceiptHandle) {
                    $request['Entries'][$Id] = compact('Id', 'ReceiptHandle');
                }
            }

            if ($request['Entries']) {
                $result = $this->client->deleteMessageBatch($request);
                foreach ($result->get('Successful') as $row) {
                    $messages[$row['Id']]->setId(null);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function requeue(MessageInterface $message)
    {
        $receiptHandle = $message->getMetadata(static::RECEIPT_HANDLE_KEY);

        if (! $message->getId() || ! $receiptHandle) {
            throw new OperationException('Message as not been in queue previously');
        }

        $this->enqueue($message);
    }

    /**
     * {@inheritDoc}
     */
    public function requeueAll(MessageCollectionInterface $messages)
    {
        foreach ($messages->getMessages() as $message) {
            $receiptHandle = $message->getMetadata(static::RECEIPT_HANDLE_KEY);
            if (! $receiptHandle || null === $message->getId()) {
                throw new OperationException('Message as not been in queue previously');
            }
        }

        $this->enqueueAll($messages);
    }

    /**
     * {@inheritDoc}
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
     * If no delay is set in the message, we fallback to the queue config message delay
     *
     * @param MessageInterface $message
     * @return int|mixed
     */
    protected function getMessageDelay(MessageInterface $message)
    {
        return $message->getDelay() === null ? $this->config->getDelaySeconds() : $message->getDelay();
    }
}