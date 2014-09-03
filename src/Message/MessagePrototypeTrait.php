<?php

namespace Qu\Message;

/**
 * Provides a standard implementation of the message interface
 */
trait MessagePrototypeTrait
{
    protected $id;

    /**
     * @var string
     */
    protected $metadata = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMetadata($name = null)
    {
        if (null === $name) {
            return $this->metadata;
        }

        return isset($this->metadata[$name]) ? $this->metadata[$name] : null;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     * @return self
     */
    public function setMetadata($name, $value = null)
    {
        if (func_num_args() === 2) {
            $name = [$name => $value];
        }

        foreach ($name as $key => $val) {
            $this->metadata[$key] = $val;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param null $value
     * @return $this
     */
    public function setData($name, $value = null)
    {
        if (func_num_args() === 2) {
            $name = [$name => $value];
        }

        foreach ($name as $key => $val) {
            $this->data[$key] = $val;
        }

        return $this;
    }

    /**
     * @param null $name
     * @return mixed
     */
    public function getData($name = null)
    {
        if (null === $name) {
            return $this->data;
        }

        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * @param int $delay
     * @return self
     */
    public function setDelay($delay)
    {
        $this->setMetadata('delay', $delay);

        return $this;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->getMetadata('delay');
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->setMetadata('priority', $priority);

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->getMetadata('priority');
    }

    /**
     * Clear the message id
     */
    public function __clone()
    {
        $this->setId(null);
    }
} 