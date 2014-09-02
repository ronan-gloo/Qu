<?php

namespace Qu\Iterator;

/**
 * @group
 *
 */
class QueueIteratorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var QueueIterator
     */
    protected $instance;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queue;
    
    public function setUp()
    {
        $this->queue    = $this->getMock('Qu\Queue\QueueInterface');
        $this->instance = new QueueIterator($this->queue);
    }

    public function testCurrentDefaults()
    {
        $this->assertNull($this->instance->current());
    }

    public function testNext()
    {
        $this->queue
            ->expects($this->exactly(2))
            ->method('count')
            ->will($this->onConsecutiveCalls(0, 1));

        $this->queue
            ->expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue($message = $this->getMock('Qu\Message\MessageInterface')));

        $this->instance->next();
        $this->instance->next();

        $this->assertSame($message, $this->instance->current());
    }

    public function testRewind()
    {
        $this->queue
            ->expects($this->exactly(2))
            ->method('count')
            ->will($this->onConsecutiveCalls(0, 1));

        $this->queue
            ->expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue($message = $this->getMock('Qu\Message\MessageInterface')));

        $this->instance->rewind();
        $this->instance->rewind();

        $this->assertSame($message, $this->instance->current());
    }

    public function testKey()
    {
        $this->assertNull($this->instance->key());
        $this->queue
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));

        $this->queue
            ->expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue($message = $this->getMock('Qu\Message\MessageInterface')));

        $message
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($expected = 123));

        $this->instance->rewind();
        $this->assertSame($expected, $this->instance->key());
    }

    public function testValid()
    {
        $this->assertFalse($this->instance->valid());

        $this->queue
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));

        $this->queue
            ->expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue($message = $this->getMock('Qu\Message\MessageInterface')));

        $this->instance->rewind();
        $this->assertTrue($this->instance->valid());
    }
}