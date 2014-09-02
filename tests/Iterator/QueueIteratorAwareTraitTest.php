<?php

namespace Qu\Iterator;

class QueueIteratorAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueueIteratorAwareTrait
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = $this->getObjectForTrait(__NAMESPACE__ . '\QueueIteratorAwareTrait');
    }

    public function testDefaultIterator()
    {
        $this->assertNull($this->instance->getIterator());
    }

    public function testIteratorAccessor()
    {
        $iterator = $this
            ->getMockBuilder('Qu\Iterator\QueueIteratorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertSame($this->instance, $this->instance->setIterator($iterator));
        $this->assertSame($iterator, $this->instance->getIterator());
    }
}
 