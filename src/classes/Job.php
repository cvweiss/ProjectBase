<?php

namespace Project\Base;

class Job
{
    public function getCron()
    {
        return Config::get("Cron:" . basename(get_class($this)), "* * * * *");
    }

    public function execute(array $params)
    {
    }

    public static function addJobs()
    {
        if (Redis::canRun(__CLASS__) === false) return;

        // Get the classmap
        $classMap = self::getClassMap();

        // Iterate through the cron classes
        $len = strlen(__NAMESPACE__ . "Cron\\");
        foreach ($classMap as $className => $location) {
            if (strncmp($className, __NAMESPACE__, $len) === 0) self::checkClass($className);
        }
    }

    public static function doJob($timeout = 60):bool
    {
        self::addJobs();
        $time = time() + $timeout;
        $queueJobs = new RedisQueue("queueJobs", $timeout);

        while (time() <= $time) {
            $job = $queueJobs->pop();

            $pid = $job === null ? 0 : pcntl_fork();
            if ($pid) return self::runJob($job);
        }
        return false;
    }

    private static function runJob($job):bool
    {
        if ($job !== null) {
            $class = $job['class'];
            $function = $job['function'];
            $args = $job['args'];
            $class::$function($args);
        }

        return true;
    }

    private static function getClassMap()
    {   
        $loader = require Config::get('projectDir') . '/vendor/autoload.php';
        return $loader->getClassMap();
    }

    private static function checkClass($className)
    {
        $class = new $className();
        if (!($class instanceof Job)) return; 

        $cron = \Cron\CronExpression::factory($class->getCron());
        if ($cron->isDue() && get_class($class) != basename(__CLASS__)) {
            $queueJobs = new RedisQueue("queueJobs");
            $job = ['class' => $className, 'function' => 'execute', 'args' => []];
            Logger::debug("Adding job $className::execute");
            $queueJobs->push($job);
        }
    }
}

