<?php

namespace Qu\Job;

/**
 * @group job
 */
class JobExecutionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobExecution
     */
    protected $instance;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|JobInterface
     */
    protected $job;
    
    public function setUp()
    {
        $this->instance = new JobExecution();
    }

    public function testDefaults()
    {
        $this->assertFalse($this->instance->processed());
        $this->assertNull($this->instance->getResult());
    }

    public function testSetStatusToSuccess()
    {
        $result = 'result';
        $this->instance->setSucceed($result);

        $this->assertTrue($this->instance->processed());
        $this->assertTrue($this->instance->succeed());
        $this->assertSame($result, $this->instance->getResult());

        $this->setExpectedException('Qu\Exception\RuntimeException');
        $this->instance->setSucceed($result);
    }

    public function testSetStatusToFailed()
    {
        $result = 'result';
        $this->instance->setFailed($result);

        $this->assertTrue($this->instance->processed());
        $this->assertTrue($this->instance->failed());
        $this->assertSame($result, $this->instance->getResult());

        $this->setExpectedException('Qu\Exception\RuntimeException');
        $this->instance->setFailed($result);
    }
}