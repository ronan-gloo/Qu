<?php

namespace Qu\Config;

class ClassMethodHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMethodHydrator
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new ClassMethodHydrator();
    }

    public function testHydrate()
    {
        $traversable = new \ArrayObject();
        $traversable['key'] = 'value';
        $traversable['foo'] = 'bar';

        $object = $this->getMock('FakeObject', ['setKey']);
        $object
            ->expects($this->exactly(2))
            ->method('setKey')
            ->with('value');

        $object
            ->expects($this->never())
            ->method('setFoo');

        $this->instance->hydrate($traversable, $object);
        $this->instance->hydrate($traversable->getArrayCopy(), $object);
    }

    /**
     * @dataProvider hydrateRequirementsDataProvider
     */
    public function testHydrateRequirements($left, $right, $valid)
    {
        if (false === $valid) {
            $this->setExpectedException('Qu\Exception\InvalidArgumentException');
        }
        $this->instance->hydrate($left, $right);
    }

    public function hydrateRequirementsDataProvider()
    {
        return [
            [['array'], new \stdClass, true],
            [new \ArrayObject, new \stdClass, true],
            [['array'], 'string', false],
            [new \ArrayObject, 'string', false],
            ['string', new \stdClass(), false]
        ];
    }
}
 