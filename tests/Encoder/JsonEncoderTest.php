<?php


namespace Qu\Encoder;


use Qu\Message\Message;

class JsonEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonEncoder
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new JsonEncoder();
    }

    public function testEncodeOptionsAccessor()
    {
        $this->assertSame($this->instance, $this->instance->setEncodeOptions($opts = [2]));
        $this->assertSame($opts, $this->instance->getEncodeOptions());
    }

    public function testDecodeOptionsAccessor()
    {
        $this->assertSame($this->instance, $this->instance->setDecodeOptions($opts = [true, 256]));
        $this->assertSame($opts, $this->instance->getDecodeOptions());
    }

    public function testEncodeMessage()
    {
        $message = $this->getMock('Qu\Message\MessageInterface');
        $message
            ->expects($this->once())
            ->method('getMeta')
            ->will($this->returnValue($meta = ['meta' => 'content']))
        ;

        $message
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data = ['data' => 'content']))
        ;

        $serialized = $this->instance->encode($message);
        $this->assertJsonStringEqualsJsonString($serialized, json_encode([
            'name' => get_class($message),
            'meta' => $meta,
            'data' => $data,
        ]));
    }

    /**
     * @depends testEncodeMessage
     */
    public function testDecodeMessage()
    {
        $message = new Message();
        $encoded = $this->instance->encode($message);
        $decoded = $this->instance->decode($encoded);
        $this->assertEquals($decoded, $message);

        $message->setMeta($data = ['test' => 'meta']);
        $message->setData($data = ['test' => 'data']);

        $encoded = $this->instance->encode($message);
        $decoded = $this->instance->decode($encoded);
        $this->assertEquals($decoded, $message);
    }
}