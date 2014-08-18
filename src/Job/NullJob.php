<?php

namespace Qu\Job;

class NullJob implements JobInterface
{
    public function execute() {}

    public function setId($id) {}

    public function getId() {}

    public function getMeta($name = null) {}

    public function setMeta($name) {}

    public function setData($name) {}

    public function getData($name = null) {}

    public function getPriority() {}

    public function getDelay() {}

    public function setPriority($priority) {}

    public function setDelay($delay) {}
}