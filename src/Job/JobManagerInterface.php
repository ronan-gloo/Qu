<?php

namespace Qu\Job;

use Qu\Message\MessageInterface;

interface JobManagerInterface
{
    /**
     * @param MessageInterface $message
     * @return JobResultInterface
     */
    public function execute(MessageInterface $message);
}