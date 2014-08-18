<?php

/**
 * The ZendJobQueue is a PHP class that implements a connection to the Job Queue Daemon
 *
 */
class ZendJobQueue
{

    /**
     * A HTTP type of job with an absolute URL
     */
    const TYPE_HTTP_RELATIVE = 0;
    /**
     * A HTTP type of job with a relative URL
     */
    const TYPE_HTTP = 1;
    /**
     * A SHELL type of job
     */
    const TYPE_SHELL = 2;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_TYPE_HTTP_RELATIVE = 1;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_TYPE_HTTP = 2;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_TYPE_SHELL = 4;
    /**
     * A low priority job
     */
    const PRIORITY_LOW = 0;
    /**
     * A normal priority job
     */
    const PRIORITY_NORMAL = 1;
    /**
     * A high priority job
     */
    const PRIORITY_HIGH = 2;
    /**
     * An urgent priority job
     */
    const PRIORITY_URGENT = 3;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_PRIORITY_LOW = 1;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_PRIORITY_NORMAL = 2;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_PRIORITY_HIGH = 4;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_PRIORITY_URGENT = 8;
    /**
     * The job is waiting to be processed
     */
    const STATUS_PENDING = 0;
    /**
     * The job is waiting for its predecessor's completion
     */
    const STATUS_WAITING_PREDECESSOR = 1;
    /**
     * The job is executing
     */
    const STATUS_RUNNING = 2;
    /**
     * Job execution has been completed successfully
     */
    const STATUS_COMPLETED = 3;
    /**
     * The job was executed and reported its successful completion status
     */
    const STATUS_OK = 4;
    /**
     * The job execution failed
     */
    const STATUS_FAILED = 5;
    /**
     * The job was executed but reported failed completion status
     */
    const STATUS_LOGICALLY_FAILED = 6;
    /**
     * Job execution timeout
     */
    const STATUS_TIMEOUT = 7;
    /**
     * A logically removed job
     */
    const STATUS_REMOVED = 8;
    /**
     * The job is scheduled to be executed at some specific time
     */
    const STATUS_SCHEDULED = 9;
    /**
     * The job execution is suspended
     */
    const STATUS_SUSPENDED = 10;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_PENDING = 1;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_WAITING_PREDECESSOR = 2;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_RUNNING = 4;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_COMPLETED = 8;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_OK = 16;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_FAILED = 32;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_LOGICALLY_FAILED = 64;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_TIMEOUT = 128;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_REMOVED = 256;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_SCHEDULED = 512;
    /**
     * (No doc, exported with reflection API)
     */
    const JOB_STATUS_SUSPENDED = 1024;
    /**
     * Disable sorting of result set of getJobsList()
     */
    const SORT_NONE = 0;
    /**
     * Sort result set of getJobsList() by job id
     */
    const SORT_BY_ID = 1;
    /**
     * Sort result set of getJobsList() by job type
     */
    const SORT_BY_TYPE = 2;
    /**
     * Sort result set of getJobsList() by job script name
     */
    const SORT_BY_SCRIPT = 3;
    /**
     * Sort result set of getJobsList() by application name
     */
    const SORT_BY_APPLICATION = 4;
    /**
     * Sort result set of getJobsList() by job name
     */
    const SORT_BY_NAME = 5;
    /**
     * Sort result set of getJobsList() by job priority
     */
    const SORT_BY_PRIORITY = 6;
    /**
     * Sort result set of getJobsList() by job status
     */
    const SORT_BY_STATUS = 7;
    /**
     * Sort result set of getJobsList() by job predecessor
     */
    const SORT_BY_PREDECESSOR = 8;
    /**
     * Sort result set of getJobsList() by job persistence flag
     */
    const SORT_BY_PERSISTENCE = 9;
    /**
     * Sort result set of getJobsList() by job creation time
     */
    const SORT_BY_CREATION_TIME = 10;
    /**
     * Sort result set of getJobsList() by job schedule time
     */
    const SORT_BY_SCHEDULE_TIME = 11;
    /**
     * Sort result set of getJobsList() by job start time
     */
    const SORT_BY_START_TIME = 12;
    /**
     * Sort result set of getJobsList() by job end time
     */
    const SORT_BY_END_TIME = 13;
    /**
     * Sort result set of getJobsList() in direct order
     */
    const SORT_ASC = 0;
    /**
     * Sort result set of getJobsList() in reverse order
     */
    const SORT_DESC = 1;
    /**
     * Constant to report completion status from the jobs using setCurrentJobStatus()
     */
    const OK = 0;
    /**
     * Constant to report completion status from the jobs using setCurrentJobStatus()
     */
    const FAILED = 1;

    /**
     * Creates a ZendJobQueue object connected to a Job Queue daemon
     *
     * @param string $queue This can be one of:
     *  1. No value specified - the default binding will be used.
     *  2. A named queue as defined in the named queues directive - In such a case, the client will connect to the binding specified by the directive, and the application name used will be the value provided.
     *  3. A literal binding URL - the URL will be used to connect to the daemon directly, and no application name will be defined.
     *  4. If a string is provided which does not match a binding URL format, and has no alias defined for it, an exception will be thrown.
     *
     *  The default value is taken from default_binding directive
     */
    final public function __construct($queue = null)
    {
        throw new \Exception('For completion purposes only !');
    }

    /**
     * Decodes an array of input variables passed to the HTTP job
     *
     * @return array The job variables
     */
    static public function getCurrentJobParams()
    {
    }

    /**
     * Reports job completion status (OK or FAILED) back to the daemon
     *
     * @param int $completion The job completion status (self::OK or self::FAILED)
     * @param string $msg The optional explanation message
     */
    static public function setCurrentJobStatus($completion, $msg = null)
    {
    }

    /**
     * (No doc, exported with reflection API)
     *
     * @return null|int
     */
    static public function getCurrentJobId()
    {
    }

    /**
     * Checks if the Job Queue Daemon is running
     *
     * @return bool Return true if the Job Queue Deamon is running, otherwise it returns false
     */
    static public function isJobQueueDaemonRunning()
    {
    }

    /**
     * Creates a new URL based job to make the Job Queue Daemon call given $script with given $vars
     *
     * @param string $url An absolute URL of the script to call
     * @param array $vars An associative array of variables which will be passed to the script. The total data size of 
     *  this array should not be greater than the size defined in the zend_jobqueue.max_message_size directive.
     * @param array $options An associative array of additional options. The elements of this array can define job 
     *  priority, predecessor, persistence, optional name, additional attributes of HTTP request as HTTP headers, etc.
     * 
     * The following options are supported:
     *  - "name" : Optional job name
     *  - "priority" : Job priority (see corresponding constants)
     *  - "predecessor" : Integer predecessor job id
     *  - "persistent" : Boolean (keep in history forever)
     *  - "schedule_time" : Time when job should be executed
     *  - "schedule" : CRON-like scheduling command
     *  - "http_headers" : Array of additional HTTP headers
     * @return int A job identifier which can be used to retrieve the job status
     */
    public function createHttpJob($url, array $vars = array(), array $options = array())
    {
    }

    /**
     * Retrieves status of previously created job identified by $job_id
     *
     * @param int $job_id A job identifier
     * @return array The array contains status, completion status and output of the job
     * 
     * Array
     * (
     *     [status] => 4
     *     [output] => HTTP/1.1 200 OK
     * Date: Mon, 11 Mar 2013 17:51:20 GMT
     * Server: Apache/2.2.23 (Amazon)
     * X-Job-Queue-Status: 0
     * Content-Length: 0
     * Connection: close
     * Content-Type: text/html; charset=UTF-8
     * 
     * 
     * )
     */
    public function getJobStatus($job_id)
    {
    }

    /**
     * Removes the job from the queue. Makes all dependent jobs fail. In case the job is in progress it will be finished
     * but dependent jobs won't be started anyway. For non-existing jobs the function just returns false. Finished jobs 
     * are simply removed from the database.
     *
     * @param int $job_id A job identifier
     * @return bool The job was removed or not removed
     */
    public function removeJob($job_id)
    {
    }

    /**
     * Restart a previously executed Job and all its followers
     *
     * @param int $job_id A job identifier
     * @return bool The job was restarted or not restarted
     */
    public function restartJob($job_id)
    {
    }

    /**
     * Checks if Queue is suspended and returns true or false
     *
     * @return bool A Job Queue status
     */
    public function isSuspended()
    {
    }

    /**
     * Suspends the Job Queue so it will accept new jobs, but won't start them. The jobs which were executed during call to this function will be completed.
     *
     */
    public function suspendQueue()
    {
    }

    /**
     * Resumes the Job Queue so it will schedule and start queued jobs
     *
     */
    public function resumeQueue()
    {
    }

    /**
     * 
     */
    public function createQueue()
    {
    }

    /**
     * 
     */
    public function deleteQueue()
    {
    }

    /**
     * Returns internal daemon statistics such as up-time, number of complete jobs, number of failed jobs, number of waiting jobs, number of currently running jobs, etc
     *
     * @return array Associative array of daemon statistics
     */
    public function getStatistics()
    {
    }

    /**
     * 
     *
     * @return array
     */
    public function getStatisticsByQueues()
    {
    }

    /**
     * Returns the current value of the configuration option of the Job Queue Daemon
     *
     * @return array Associative array of configuration variables
     */
    public function getConfig()
    {
    }

    /**
     * Re-reads the configuration file of the Job Queue Daemon and reloads all directives that are reloadable
     *
     * @return bool The configuration file was loaded successfully or not
     */
    public function reloadConfig()
    {
    }

    /**
     * Returns an associative array with properties of the job with the given id from the daemon database
     *
     * @param int $job_id A job identifier
     * @return array Array of job details. The following properties are provided (some of them don't have to always be set):
     *  - "id" : The job identifier
     *  - "type" : The job type (see self::TYPE_* constants)
     *  - "status" : The job status (see self::STATUS_* constants)
     *  - "priority" : The job priority (see self::PRIORITY_* constants)
     *  - "persistent" : The persistence flag
     *  - "script" : The URL or SHELL script name
     *  - "predecessor" : The job predecessor
     *  - "name" : The job name
     *  - "vars" : The input variables or arguments
     *  - "http_headers" : The additional HTTP headers for HTTP jobs
     *  - "output" : The output of the job
     *  - "error" : The error output of the job
     *  - "creation_time" : The time when the job was created
     *  - "start_time" : The time when the job was started
     *  - "end_time" : The time when the job was finished
     *  - "schedule" : The CRON-like schedule command
     *  - "schedule_time" : The time when the job execution was scheduled
     *  - "app_id" : The application name
     */
    public function getJobInfo($job_id)
    {
    }

    /**
     * 
     *
     * @return array
     */
    public function getQueues()
    {
    }

    /**
     * Returns a list of associative arrays with the properties of the jobs which depend on the job with the given identifier
     *
     * @param int $job_id A job identifier
     * @return array A list of jobs
     */
    public function getDependentJobs($job_id)
    {
    }

    /**
     * Returns a list of associative arrays with properties of jobs which conform to a given query
     *
     * @param array $filter An associative array with query arguments.
     * The array may contain the following keys which restrict the resulting list:
     *  - "app_id" : Query only jobs which belong to the given application
     *  - "name" : Query only jobs with the given name
     *  - "script" : Query only jobs with a script name similar to the given one (SQL LIKE)
     *  - "type" : Query only jobs of the given types (bitset)
     *  - "priority" : Query only jobs with the given priorities (bitset)
     *  - "status" : Query only jobs with the given statuses (bitset)
     *  - "rule_id" : Query only jobs produced by the given scheduling rule
     *  - "scheduled_before" : Query only jobs scheduled before the given date
     *  - "scheduled_after" : Query only jobs scheduled after the given date
     *  - "executed_before" : Query only jobs executed before the given date
     *  - "executed_after" : Query only jobs executed after the given date
     *  - "sort_by" : Sort by the given field (see self::SORT_BY_* constants)
     *  - "sort_direction" : Sort the order (self::SORT_ASC or self::SORT_DESC)
     *  - "start" : Skip the given number of jobs
     *  - "count" : Retrieve only the given number of jobs (100 by default)
     * 
     * array(
     *     'name'=> $query_array,
     *     'status'=> 1<< ZendJobQueue::STATUS_COMPLETED, // You must shift bit by one for status query wtf Zend?
     *     'sort_by'=> ZendJobQueue::SORT_BY_TYPE ,
     *     'sort_direction'=> ZendJobQueue::SORT_ASC,
     *     'count'=>1
     * );
     * @param int $total The output parameter which is set to the total number of jobs conforming to the given query, ignoring "start" and "count" fields
     * @return array A list of jobs with their details
     */
    public function getJobsList(array $filter = array(), $total = null)
    {
    }

    /**
     * Returns an array of application names known by the daemon
     *
     * @return array A list of applications
     */
    public function getApplications()
    {
    }

    /**
     * Returns an array of all the registered scheduled rules.
     *
     * Each rule is represented by a nested associative array with the following properties:
     *  - "id" : The scheduling rule identifier
     *  - "status" : The rule status (see self::STATUS_* constants)
     *  - "type" : The rule type (see self::TYPE_* constants)
     *  - "priority" : The priority of the jobs created by this rule
     *  - "persistent" : The persistence flag of the jobs created by this rule
     *  - "script" : The URL or script to run
     *  - "name" : The name of the jobs created by this rule
     *  - "vars" : The input variables or arguments
     *  - "http_headers" : The additional HTTP headers
     *  - "schedule" : The CRON-like schedule command
     *  - "app_id" : The application name associated with this rule and created jobs
     *  - "last_run" : The last time the rule was run
     *  - "next_run" : The next time the rule will run
     *
     * @return array A list of scheduling rules
     */
    public function getSchedulingRules()
    {
    }

    /**
     * Returns an associative array with the properties of the scheduling rule identified by the given argument.
     *
     * The list of the properties is the same as in getSchedulingRule().
     *
     * @param int $rule_id The rule identifier
     * @return array Information about the scheduling rule
     */
    public function getSchedulingRule($rule_id)
    {
    }

    /**
     * Deletes the scheduling rule identified by the given $rule_id and scheduled jobs created by this rule
     *
     * @param int $rule_id The rule identifier
     * @return bool The scheduling rule was deleted or not deleted
     */
    public function deleteSchedulingRule($rule_id)
    {
    }

    /**
     * Suspends the scheduling rule identified by given $rule_id and deletes scheduled jobs created by this rule
     *
     * @param int $rule_id The rule identifier
     * @return bool The scheduling rule was suspended or not suspended
     */
    public function suspendSchedulingRule($rule_id)
    {
    }

    /**
     * Resumes the scheduling rule identified by given $rule_id and creates a corresponding scheduled job
     *
     * @param int $rule_id The rule identifier
     * @return bool The scheduling rule was resumed or not resumed
     */
    public function resumeSchedulingRule($rule_id)
    {
    }

    /**
     * Updates and reschedules the existing scheduling rule
     *
     * @param int $rule_id The rule identifier
     * @param string $script The URL to request
     * @param array $vars The input variables
     * @param array $options The same as in createHttpJob()
     * @return bool The scheduling rule was updated or not updated
     */
    public function updateSchedulingRule($rule_id, $script, array $vars = null, array $options = null)
    {
    }
}
