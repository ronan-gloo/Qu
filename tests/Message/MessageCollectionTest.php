<?php

namespace Qu\Message;

class MessageCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageCollection
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new MessageCollection();
    }

    public function testConstructorAcceptsTraversable()
    {
        $message  = $this->getMock('Qu\Message\MessageInterface');
        $messages = [$message, $message];

        $instance = new MessageCollection($messages);
        $this->assertSame($messages, $instance->getMessages());

        // only arrays are tolerated
        $this->setExpectedException('PHPUnit_Framework_Error');
        new MessageCollection(new \ArrayObject($messages));
    }

    public function testAddMessageArgument()
    {
        $message = $this->getMock('Qu\Message\MessageInterface');
        $this->assertSame($this->instance, $this->instance->addMessage($message), 'Method is fluent');

        $this->assertCount(1, $this->instance->getMessages());
        $this->assertSame($message, $this->instance->getMessages()[0]);

        $this->instance->addMessage($message);
        $this->assertCount(2, $this->instance->getMessages());
        $this->assertSame([$message, $message], $this->instance->getMessages());

        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->instance->addMessage(new \stdClass(), 'only instances of MessageInterface are accepted');
    }

    public function testAddMessageWithQueueDefaultDelay()
    {
        $message = $this->getMock('Qu\Message\MessageInterface');
        $message
            ->expects($this->exactly(1))
            ->method('setDelay')
            ->with($collectionDelay = 10)
        ;

        $message
            ->expects($this->exactly(2))
            ->method('getDelay')
            ->will($this->onConsecutiveCalls(null, 0));
        ;

        $this->instance->addMessage($message);
        $this->instance->setDelay($collectionDelay);
        $this->instance->addMessage($message);
        $this->instance->addMessage($message);
    }

    public function testAddMessageWithQueueDefaultPriority()
    {
        $message = $this->getMock('Qu\Message\MessageInterface');
        $message
            ->expects($this->exactly(1))
            ->method('setPriority')
            ->with($priority = 10)
        ;

        $message
            ->expects($this->exactly(2))
            ->method('getPriority')
            ->will($this->onConsecutiveCalls(null, 0));
        ;

        $this->instance->addMessage($message);
        $this->instance->setPriority($priority);
        $this->instance->addMessage($message);
        $this->instance->addMessage($message);
    }

    public function testCountCollection()
    {
        $message = $this->getMock('Qu\Message\MessageInterface');
        $this->instance->addMessage($message)->addMessage($message);

        $this->assertCount(2, $this->instance);
    }

    public function testIteratorImplementation()
    {
        $this->assertInstanceOf('IteratorAggregate', $this->instance, 'be sure that PHP handle getIterator');

        $message = $this->getMock('Qu\Message\MessageInterface');
        $this->instance->addMessage($message)->addMessage($message);

        $iterator = $this->instance->getIterator();
        $this->assertSame([$message, $message], $iterator->getArrayCopy(), 'iterator contains messages');
    }
}