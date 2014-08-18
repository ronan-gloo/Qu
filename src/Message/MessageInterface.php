<?php

namespace Qu\Message;

/**
 * Interface MessageInterface
 * @package Qu\Queue
 */
interface MessageInterface
{
    const PRIORITY_LOW    = 1;
    const PRIORITY_NORMAL = 2;
    const PRIORITY_HIGH   = 3;
    const PRIORITY_URGENT = 4;

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $name
     * @return mixed
     */
    public function getMeta($name = null);

    /**
     * @param $name
     * @return self
     */
    public function setMeta($name);

    /**
     * @param string $name
     * @return self
     */
    public function setData($name);

    /**
     * @param null|string $name
     * @return mixed
     */
    public function getData($name = null);

    /**
     * @param integer $priority seconds
     * @return self
     */
    public function setPriority($priority);

    /**
     * @return integer
     */
    public function getPriority();

    /**
     * @return integer
     */
    public function getDelay();

    /**
     * @param int $delay seconds
     * @return self
     */
    public function setDelay($delay);
}