<?php

namespace Qu\Message;

/**
 * @group message
 *
 */
class MessagePrototypeTraitTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var MessagePrototypeTrait
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = $this->getObjectForTrait(__NAMESPACE__ . '\MessagePrototypeTrait');
    }

    public function testIdAccessor()
    {
        $this->assertNull($this->instance->getId());
        $this->assertSame($this->instance, $this->instance->setId($id = '2'));
        $this->assertSame($id, $this->instance->getId());
    }

    public function testPriorityAccessor()
    {
        $this->assertNull($this->instance->getPriority());
        $this->assertSame($this->instance, $this->instance->setPriority($priority = -100));
        $this->assertSame($priority, $this->instance->getPriority());
    }

    public function testDelayAccessor()
    {
        $this->assertNull($this->instance->getDelay());
        $this->assertSame($this->instance, $this->instance->setDelay($delay = 100));
        $this->assertSame($delay, $this->instance->getDelay());
    }

    public function testMetadataAccessor()
    {
        $this->assertSame([], $this->instance->getMetadata());
        $this->assertNull($this->instance->getMetadata('void'));

        $this->assertSame($this->instance, $this->instance->setMetadata('key', 'value'));
        $this->assertSame('value', $this->instance->getMetadata('key'));
        $this->assertSame(['key' => 'value'], $this->instance->getMetadata());
        $this->assertSame($this->instance, $this->instance->setMetadata($expected = ['value' => 'key']));
        $this->assertSame(['key' => 'value', 'value' => 'key'], $this->instance->getMetadata());
    }

    public function testDataAccessor()
    {
        $this->assertSame([], $this->instance->getData());
        $this->assertNull($this->instance->getData('void'));

        $this->assertSame($this->instance, $this->instance->setData('key', 'value'));
        $this->assertSame('value', $this->instance->getData('key'));
        $this->assertSame(['key' => 'value'], $this->instance->getData());
        $this->assertSame($this->instance, $this->instance->setData($expected = ['value' => 'key']));
        $this->assertSame(['key' => 'value', 'value' => 'key'], $this->instance->getData());
    }

    public function testClone()
    {
        $this->instance->setId(12);
        $clone = clone $this->instance;
        $this->assertNull($clone->getId());
    }
}