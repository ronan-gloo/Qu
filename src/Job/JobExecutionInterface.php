<?php

namespace Qu\Job;

interface JobExecutionInterface
{
    const STATUS_UNPROCESSED = 0;
    const STATUS_SUCCEED     = 1;
    const STATUS_FAILED      = 2;

    /**
     * Is the execution has been done yet ?
     *
     * @return bool
     */
    public function processed();

    /**
     * Is the execution succeed ?
     *
     * @return bool
     */
    public function succeed();

    /**
     * Is the execution failed ?
     *
     * @return bool
     */
    public function failed();

    /**
     * @return mixed
     */
    public function getResult();
}