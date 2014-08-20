<?php

namespace Qu\Serializer;

class SerializerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializerAwareTrait
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = $this->getObjectForTrait(__NAMESPACE__ . '\SerializerAwareTrait');
    }

    public function testSerializerAccessor()
    {
        $serializer = $this->getMock(__NAMESPACE__ . '\SerializerInterface');
        $this->assertSame($this->instance, $this->instance->setSerializer($serializer));
        $this->assertSame($serializer, $this->instance->getSerializer());

        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->instance->setSerializer(new \ArrayObject());
    }
}
 