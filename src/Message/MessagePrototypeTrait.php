<?php

namespace Qu\Message;

/**
 * Provides a standard implementation of the message interface
 */
trait MessagePrototypeTrait
{
    /**
     * @var string
     */
    protected $meta = [];

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
        $this->setMeta('id', $id);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getMeta('id');
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMeta($name = null)
    {
        if (null === $name) {
            return $this->meta;
        }

        return isset($this->meta[$name]) ? $this->meta[$name] : null;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     * @return self
     */
    public function setMeta($name, $value = null)
    {
        if (func_num_args() === 2) {
            $name = [$name => $value];
        }

        foreach ($name as $key => $val) {
            $this->meta[$key] = $val;
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
     * @return self
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
        $this->setMeta('delay', $delay);

        return $this;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->getMeta('delay');
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->setMeta('priority', $priority);

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->getMeta('priority');
    }

    /**
     * Clear the message id
     */
    public function __clone()
    {
        $this->setId(null);
    }
} 