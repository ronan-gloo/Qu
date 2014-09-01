<?php

namespace Qu\Job;

class NullJob implements JobInterface
{
    public function execute() {}

    public function setId($id) {}

    public function getId() {}

    public function getMetadata($name = null) {}

    public function setMetadata($name) {}

    public function setData($name) {}

    public function getData($name = null) {}

    public function getPriority() {}

    public function getDelay() {}

    public function setPriority($priority) {}

    public function setDelay($delay) {}
}