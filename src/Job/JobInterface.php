<?php

namespace Qu\Job;

use Qu\Message\MessageInterface;

interface JobInterface extends MessageInterface
{
    /**
     * Invoke the job execution
     * JobResultInterface must acquire a status before to be returned
     *
     * @return JobExecutionInterface
     */
    public function execute();
}