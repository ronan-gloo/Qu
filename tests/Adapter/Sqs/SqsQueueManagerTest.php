<?php

namespace Qu\Adapter\Sqs;

/**
 * @group adapter
 * @group sqs
 */
class SqsQueueManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqsQueueManager
     */
    protected $instance;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $client, $config;
    
    public function setUp()
    {
        $this->client = $this
            ->getMockBuilder('Aws\Sqs\SqsClient')
            ->disableOriginalConstructor()
            ->setMethods(['listQueues', 'setQueueAttributes', 'getBaseUrl', 'deleteQueue'])
            ->getMock();

        $this->config   = $this->getMock(__NAMESPACE__ . '\SqsQueueManagerConfig');
        $this->instance = new SqsQueueManager($this->client, $this->config);
    }

    public function testHas()
    {
        $model = $this->getMock('Guzzle\Service\Resource\Model');
        $model
            ->expects($this->any())
            ->method('getPath')
            ->with('QueueUrls')
            ->will($this->onConsecutiveCalls(['test-me'], ['test-']));

        $this->config
            ->expects($this->any())
            ->method('getQueueNamePrefix')
            ->will($this->returnValue('test-'))
        ;
        $this->client
            ->expects($this->any())
            ->method('listQueues')
            ->with(['QueueNamePrefix' => 'test-'])
            ->will($this->returnValue($model))
        ;

        $this->assertTrue($this->instance->has('me'));
        $this->assertFalse($this->instance->has('you'));
    }

    public function testUpdate()
    {
        $config = new SqsQueueConfig();
        $config->setName('name');
        $config->setAccountId('123456789012');
        $this->client
            ->expects($this->once())
            ->method('setQueueAttributes')
            ->with(['QueueUrl' => '/123456789012/name', 'Attributes' => $config->toAttributes()])
        ;
        $this->instance->update(new SqsQueue($this->client, $config));

        $this->setExpectedException('Qu\Exception\InvalidArgumentException');
        $this->instance->update($this->getMock('Qu\Queue\QueueAdapterInterface'));
    }

    public function testFlush()
    {
        $queue = $this
            ->getMockBuilder(__NAMESPACE__ . '\SqsQueue')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $queue
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1))
        ;
        $queue
            ->expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue($msg = $this->getMock('Qu\Message\MessageInterface')))
        ;
        $queue
            ->expects($this->once())
            ->method('remove')
            ->with($msg)
        ;
        $this->instance->flush($queue);

        $this->setExpectedException('Qu\Exception\InvalidArgumentException');
        $this->instance->flush($this->getMock('Qu\Queue\QueueAdapterInterface'));
    }

    public function testRemove()
    {
        $queue = $this
            ->getMockBuilder(__NAMESPACE__ . '\SqsQueue')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $queue
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url = 'test'))
        ;

        $this->client
            ->expects($this->any())
            ->method('deleteQueue')
            ->with(['QueueUrl' => $url])
        ;

        $this->instance->remove($queue);

        $this->setExpectedException('Qu\Exception\InvalidArgumentException');
        $this->instance->remove($this->getMock('Qu\Queue\QueueAdapterInterface'));
    }

    public function testClientThrowsExceptionOnRemove()
    {
        $queue = $this
            ->getMockBuilder(__NAMESPACE__ . '\SqsQueue')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->client
            ->expects($this->once())
            ->method('deleteQueue')
            ->will($this->returnCallback(function(){
                throw new \Exception;
            }))
        ;

        $this->setExpectedException('Qu\Exception\RuntimeException');
        $this->instance->remove($queue);
    }
}