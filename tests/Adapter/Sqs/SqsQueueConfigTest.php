<?php

namespace Qu\Adapter\Sqs;

/**
 * @group adapter
 * @group sqs
 * @group config
 */
class SqsQueueConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqsQueueConfig
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new SqsQueueConfig();
    }

    /**
     * @dataProvider delaySecondsDataProvider
     */
    public function testDelaySecondsAccessor($value, $expected)
    {
        $this->assertSame($this->instance, $this->instance->setDelaySeconds($value));
        $this->assertSame($expected, $this->instance->getDelaySeconds());
    }

    public function delaySecondsDataProvider()
    {
        return [
            [-1, 0],
            [0, 0],
            [1, 1],
            [1.5, 2],
            ['27', 27],
            ['27.2', 27],
            [900, 900],
            [901, 900],
        ];
    }

    /**
     * @dataProvider MessageSizeDataProvider
     */
    public function testMessageMaxSizeAccessor($value, $expected)
    {
        $this->assertSame($this->instance, $this->instance->setMaximumMessageSize($value));
        $this->assertSame($expected, $this->instance->getMaximumMessageSize());
    }

    public function messageSizeDataProvider()
    {
        return [
            [-1, 125],
            [0, 125],
            [126, 126],
            [126.1, 126],
            ['127', 127],
            ['127.5', 128],
            [262144, 262144],
            [262145, 262144],
        ];
    }

    /**
     * @dataProvider visibilityTimeoutDataProvider
     */
    public function testVisibilityTimeoutAccessor($value, $expected)
    {
        $this->assertSame($this->instance, $this->instance->setVisibilityTimeout($value));
        $this->assertSame($expected, $this->instance->getVisibilityTimeout());
    }

    public function visibilityTimeoutDataProvider()
    {
        return [
            [-1, 0],
            [0, 0],
            [1, 1],
            [1.5, 2],
            ['27.2', 27],
            [43200, 43200],
            [43201, 43200],
        ];
    }

    /**
     * @dataProvider receiveMessageWaitTimeSecondsDataProvider
     */
    public function testReceiveMessageWaitTimeSecondsAccessor($value, $expected)
    {
        $this->assertSame($this->instance, $this->instance->setReceiveMessageWaitTimeSeconds($value));
        $this->assertSame($expected, $this->instance->getReceiveMessageWaitTimeSeconds());
    }

    public function receiveMessageWaitTimeSecondsDataProvider()
    {
        return [
            [-1, 0],
            [0, 0],
            [1, 1],
            [1.5, 2],
            ['17.2', 17],
            [20, 20],
            [21, 20],
        ];
    }

    /**
     * @dataProvider MessageRetentionPeriodDataProvider
     */
    public function testMessageRetentionPeriod($value, $expected)
    {
        $this->assertSame($this->instance, $this->instance->setMessageRetentionPeriod($value));
        $this->assertSame($expected, $this->instance->getMessageRetentionPeriod());
    }

    public function MessageRetentionPeriodDataProvider()
    {
        return [
            [-1, 60],
            [61, 61],
            [61.5, 62],
            ['67.2', 67],
            [345600, 345600],
            [345601, 345600],
        ];
    }

    /**
     * @dataProvider batchSizeAccessorDataProvider
     */
    public function testBatchSizeAccessor($value, $expected)
    {
        $this->assertSame($this->instance, $this->instance->setBatchSize($value));
        $this->assertSame($expected, $this->instance->getBatchSize());
    }

    public function batchSizeAccessorDataProvider()
    {
        return [
            [-1, 1],
            [0, 1],
            [3.3, 3],
            [3.5, 4],
            [10, 10],
            [11, 10],
        ];
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
     * @dataProvider nameDataProvider
     */
    public function testNameAccessor($value, $isValid)
    {
        if (false === $isValid) {
            $this->setExpectedException('Qu\Exception\InvalidArgumentException');
            $this->instance->setName($value);
        } else {
            $this->assertSame($this->instance, $this->instance->setName($value));
            $this->assertEquals(trim($value), $this->instance->getName());
        }
    }

    public function nameDataProvider()
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

    public function testToAttributesWithSkippedProperties()
    {
        $this->instance
            ->setAccountId('123456789101')
            ->setName('A-Name')
            ->setBatchSize(9)
            ->setDelaySeconds(12)
            ->setVisibilityTimeout(10)
            ->setMaximumMessageSize(1000)
            ->setReceiveMessageWaitTimeSeconds(10)
            ->setMessageRetentionPeriod(70)
        ;

        $expected = [
            'DelaySeconds' => 12,
            'VisibilityTimeout' => 10,
            'MaximumMessageSize' => 1000,
            'ReceiveMessageWaitTimeSeconds' => 10,
            'MessageRetentionPeriod' => 70
        ];

        $attributes = $this->instance->toAttributes();
        $this->assertSame($expected, $attributes);
    }

    public function testConstructorWithArrayConfig()
    {
        $config   = ['delay_seconds' => 22];
        $instance = new SqsPriorityQueueConfig($config);
        $this->assertSame(22, $instance->getDelaySeconds());
    }
}
 