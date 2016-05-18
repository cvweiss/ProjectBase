<?php

namespace Project\Base\Cron;

use Project\Base\Config;
use Project\Base\Redis;
use Project\Base\RedisQueue;
use Project\Base\Logger;

class Job
{
    public function getCron()
    {
        return Config::get("Cron:" . basename(get_class($this)), "* * * * *");
    }

    public function execute()
    {
    }

    public static function addJobs()
    {
        if (Redis::canRun(__CLASS__) === false) return;

        // Get the classmap
        $classMap = self::getClassMap();

        // Iterate through the cron classes
        $len = strlen(__NAMESPACE__);
        foreach ($classMap as $className => $location) {
            if (strncmp($className, __NAMESPACE__, $len) === 0) self::checkClass($className);
        }
    }

    public static function doJob()
    {
        self::addJobs();

        $queueJobs = new RedisQueue("queueJobs");
        $job = $queueJobs->pop();

        if ($job === null) return;

        $class = $job['class'];
        $function = $job['function'];
        $args = $job['args'];
        $class::$function($args);
    }

    private static function getClassMap()
    {   
        $loader = require Config::get('projectDir') . '/vendor/autoload.php';
        return $loader->getClassMap();
    }

    private static function checkClass($className)
    {
        $class = new $className();
        if (!($class instanceof Job)) throw new \RuntimeException("$className is not an instance of " . __CLASS__);

        $cron = \Cron\CronExpression::factory($class->getCron());
        if ($cron->isDue() && get_class($class) != basename(__CLASS__)) {
            $queueJobs = new RedisQueue("queueJobs");
            $job = ['class' => $className, 'function' => 'execute', 'args' => []];
            Logger::debug("Adding job $className::execute");
            $queueJobs->push($job);
        }
    }
}

