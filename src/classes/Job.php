<?php

namespace Project\Base;

class Job
{
    public static function doJobs($timeout = 60):bool
    {
        self::addJobs();
        $maxChildren = Config::get("maxJobChildren", 20); 

        $time = time() + $timeout;
        $queueJobs = new JobQueue();
        $children = [];

        while (time() <= $time) {
            $job = $queueJobs->pop();

            $pid = ($job === null) ? 0 : pcntl_fork();
            if ($job !== null && $pid === 0) return self::runJob($job);
            $children[$pid] = true;
            self::checkChildren($maxChildren, $children);
        }
        return false;
    }

    private static function checkChildren(int $maxChildren, array &$children):bool
    {
        while (count($children) >= $maxChildren) {
            $status = null;
            $pidDone = pcntl_waitpid(0, $status);
            unset($children[$pidDone]);
        }
        return count($children) > $maxChildren;
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

    public static function addJob(string $className, string $function, array $args = [], int $priority = 0)
    {
        $queueJobs = new JobQueue();
        $job = ['class' => $className, 'function' => $function, 'args' => $args];
        $queueJobs->push($job, $priority);
    }
}

