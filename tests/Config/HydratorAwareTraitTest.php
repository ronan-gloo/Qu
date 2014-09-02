<?php

namespace Qu\Config;

class HydratorAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HydratorAwareTrait
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = $this->getObjectForTrait(__NAMESPACE__ . '\HydratorAwareTrait');
    }

    public function testGetHydratorLazyLoadClassMethods()
    {
        $this->assertInstanceOf('Qu\Config\ClassMethodHydrator', $this->instance->getHydrator());
    }

    public function testSetHydrator()
    {
        $hydrator = $this->getMock('Qu\Config\HydratorInterface');
        $this->assertSame($this->instance, $this->instance->setHydrator($hydrator));
        $this->assertSame($hydrator, $this->instance->getHydrator());
    }

    public function testHydrate()
    {
        $hydrator = $this->getMock('Qu\Config\HydratorInterface');
        $hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($arg = ['to hydrate'], $this->identicalTo($this->instance))
            ->will($this->returnValue(new \stdClass()));

        $this->instance->setHydrator($hydrator);
        $this->assertSame($this->instance, $this->instance->hydrate($arg));
    }
}