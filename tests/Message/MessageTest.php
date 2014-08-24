<?php

namespace Qu\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Message
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new Message();
    }

    public function testMessageConstructor()
    {
        $message = new Message('test');
        $this->assertSame(['body' => 'test'], $message->getData());
        $this->assertSame([], $message->getMeta());

        $this->assertNull($message->getId());
        $this->assertNull($message->getPriority());
        $this->assertNull($message->getDelay());
    }
}