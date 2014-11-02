<?php

namespace Qu\Iterator;

use Qu\Queue\QueueInterface;
use Traversable;

class QueueIteratorAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueueIteratorAwareTrait
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new QueueIteratorTraitStub;
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

    public function testDefaultIteratorIsCreatedIfNotSet()
    {
        $iterator = $this->instance->getIterator();
        $this->assertInstanceOf('Qu\Iterator\QueueIterator', $iterator);
    }
}
class QueueIteratorTraitStub implements QueueInterface
{
    use QueueIteratorAwareTrait;
    public function enqueue($message) {}
    public function requeue($message) {}
    public function dequeue() {}
    public function remove($message) {}
    public function count(){}
}