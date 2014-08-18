<?php

namespace Qu\Job;

interface JobExecutionInterface
{
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