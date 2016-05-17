<?php

namespace Project\Base\Cron;

use Project\Base\Config;
use Project\Base\Redis;
use Project\Base\RedisQueue;

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
        $redis = Redis::getRedis();
        $time = time();
        $time = $time - ($time % 60);
        if ($redis->setNX("cron:$time", true) === false) return;

        // Find composer
        $classes = get_declared_classes();
        $loader = null;
        foreach ($classes as $class) {
            if (substr($class, 0, 18) == "ComposerAutoloader") {
                $loader = $class::getLoader();
                break;
            }
        }
        if ($loader === null) throw new \RuntimeException("Unable to locate composer's loader");

        // Get the classmap
        $classMap = $loader->getClassMap();
        $queueJobs = new RedisQueue("queueJobs");
        $len = strlen(__NAMESPACE__);
        foreach ($classMap as $className => $location) {
            if (substr($className, 0, $len) == __NAMESPACE__) {
                if ($className === __CLASS__) continue; // Don't execute ourself
                $class = new $className();
                if (!($class instanceof Job)) throw new \RuntimeException("$className is not an instance of " . __CLASS__);
                $cron = \Cron\CronExpression::factory($class->getCron());
                if ($cron->isDue()) {
                    $job = ['class' => $className, 'function' => 'execute', 'args' => []];
                    $queueJobs->push($job);
                }
            }
        }
    }

    public static function doJob()
    {
        $queueJobs = new RedisQueue("queueJobs");
        $job = $queueJobs->pop();

        if ($job === null) return;

        $class = $job['class'];
        $function = $job['function'];
        $args = $job['args'];
        $class::$function($args);
    }
}

