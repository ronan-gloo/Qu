<?php

namespace Qu\Job;

interface JobResultInterface
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED  = 2;

    /**
     * @return mixed
     */
    public function getStatus();
}