<?php

namespace Project\Base;

class Job
{
    public static function doJobs($timeout = 60):bool
    {
        self::addJobs();

        $time = time() + $timeout;
        $queueJobs = new RedisQueue("queueJobs", $timeout);

        while (time() <= $time) {
            $job = $queueJobs->pop();

            $pid = $job === null ? 0 : pcntl_fork();
            if ($pid == 0) return self::runJob($job);
        }
        return false;
    }

    private static function runJob($job):bool
    {
        if ($job !== null) {
            $class = new $job['class'];
            $function = $job['function'];
            $args = $job['args'];
            $class->$function($args);
        }

        return true;
    }

    public static function addJobs()
    {   
        if (Redis::canRun(__CLASS__) === false) return;

        $jobs = Config::get("cronjobs", []);
        foreach ($jobs as $className) {
            self::checkClass('\\' . $className);
        }
    }

    private static function checkClass($className)
    {
        $class = new $className();

        if (!($class instanceof CronJob)) throw new IllegalException("$className is not an instanceof \\Project\\Base\\CronJob");

        $cron = \Cron\CronExpression::factory($class->getCron());
        if ($cron->isDue() && get_class($class) != basename(__CLASS__)) {
            self::addJob($className, 'execute', []);
        }
    }

    public static function addJob(string $className, string $function, array $args = [])
    {
        $queueJobs = new RedisQueue("queueJobs");
        $job = ['class' => $className, 'function' => 'execute', 'args' => $args];
        Logger::debug("Adding job $className::$function");
        $queueJobs->push($job);
    }
}

