<?php

namespace Qu\Encoder;

class EncoderAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EncoderAwareTrait
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = $this->getObjectForTrait(__NAMESPACE__ . '\EncoderAwareTrait');
    }

    public function testSerializerAccessor()
    {
        $this->assertInstanceOf(
            __NAMESPACE__ . '\JsonEncoder',
            $this->instance->getEncoder(),
            'Default Encoder driven by json'
        );

        $serializer = $this->getMock(__NAMESPACE__ . '\EncoderInterface');
        $this->assertSame($this->instance, $this->instance->setEncoder($serializer));
        $this->assertSame($serializer, $this->instance->getEncoder());

        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->instance->setEncoder(new \ArrayObject());
    }
}
 