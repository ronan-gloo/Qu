<?php

namespace Qu\Adapter\Sqs;

/**
 * @group adapter
 * @group sqs
 * @group config
 */
class SqsQueueManagerConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqsQueueManagerConfig
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new SqsQueueManagerConfig();
    }

    public function testCreateIfNotFound()
    {
        $this->assertFalse($this->instance->getCreateNotFound());

        $this->assertSame($this->instance, $this->instance->setCreateNotFound(1));
        $this->assertTrue($this->instance->getCreateNotFound());
    }

    /**
     * @dataProvider accountIdDataProvider
     */
    public function testAccountIdAccessor($value, $isValid)
    {
        if (false === $isValid) {
            $this->setExpectedException('Qu\Exception\InvalidArgumentException');
            $this->instance->setAccountId($value);
        } else {
            $this->assertSame($this->instance, $this->instance->setAccountId($value));
            $this->assertSame($value, $this->instance->getAccountId());
        }
    }

    public function accountIdDataProvider()
    {
        return [
            [123982938238, true],
            ['003982938238', true],
            ['1003982938238', false],
            ['10039829382A', false],
            [1909872387.123, false],
        ];
    }

    /**
     * @dataProvider queueNamePrefixDataProvider
     */
    public function testQueueNamePrefixAccessor($value, $isValid)
    {
        if (false === $isValid) {
            $this->setExpectedException('Qu\Exception\InvalidArgumentException');
            $this->instance->setQueueNamePrefix($value);
        } else {
            $this->assertSame($this->instance, $this->instance->setQueueNamePrefix($value));
            $this->assertEquals(trim($value), $this->instance->getQueueNamePrefix());
        }
    }

    public function queueNamePrefixDataProvider()
    {
        return [
            [str_repeat('a', 80), true],
            [str_repeat('a', 81), false],
            ['123456', true],
            [123456, true],
            [123456.12, false],
            ['ab-_ssdWU', true],
            ['ab-&ssdWU', false],
            ['ab:ssd', false],
            ['', false],
            [' 0 ', true],
        ];
    }
}
 