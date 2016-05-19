<?php

namespace Project\Base;

class Redis
{
    private static $redis = null;

    public static function getRedis()
    {
        if (self::$redis === null) {
            self::$redis = new \Redis();
            self::$redis->connect(Config::get('redisServer', '127.0.0.1'), Config::get('redisPort', 6379), 3600);
        }
        return self::$redis;
    }

    public static function canRun(string $key, int $mutex = 60)
    {
        $redis = self::getRedis();
        $time = time();
        $time = $time - ($time % $mutex);
        
        $key = "$key:$time";
        $locked = $redis->set($key, true, Array('nx', 'ex' => $mutex));
        return $locked;
    }
}
