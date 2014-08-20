<?php


namespace Qu\Serializer;


class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonSerializer
     */
    protected $instance;
    
    public function setUp()
    {
        $this->instance = new JsonSerializer();
    }

    public function testEncodeOptionsAccessor()
    {
        $this->assertSame($this->instance, $this->instance->setEncodeOptions($opts = [2]));
        $this->assertSame($opts, $this->instance->getEncodeOptions());
    }

    public function testDecodeOptionsAccessor()
    {
        $this->assertSame($this->instance, $this->instance->setDecodeOptions($opts = [true, 256]));
        $this->assertSame($opts, $this->instance->getDecodeOptions());
    }
}
 