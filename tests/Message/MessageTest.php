<?php

namespace Qu\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageConstructorWithString()
    {
        $message = new Message('test');
        $this->assertSame(['body' => 'test'], $message->getData());
        $this->assertSame([], $message->getMetadata());

        $this->assertNull($message->getId());
        $this->assertNull($message->getPriority());
        $this->assertNull($message->getDelay());
    }

    public function testMessageConstructorWithTraversableObjects()
    {
        $body = new \ArrayObject;
        $body['content'] = 'Hello';

        $message = new Message($body, $body);
        $this->assertSame(['content' => 'Hello'], $message->getData());
        $this->assertSame(['content' => 'Hello'], $message->getMetadata());
    }

    /**
     * @dataProvider constructorWithWrongMetadataProvider
     * @expectedException \Qu\Exception\InvalidArgumentException
     */
    public function testMessageConstructorWithWrongMetadata($metadata)
    {
        new Message('test', $metadata);
    }

    public function constructorWithWrongMetadataProvider()
    {
        return [
            ['string'],
            [123],
            [tmpfile()],
            [new \stdClass()]
        ];
    }
}