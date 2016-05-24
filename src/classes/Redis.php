<?php

namespace Project\Base;

class Redis
{
    private static $pid = 0;
    private static $redis = null;

    public static function getRedis()
    {
        if (self::$redis === null || self::$pid !== getmypid()) {
            self::$redis = new \Redis();
            self::$redis->connect(Config::get('redisServer', '127.0.0.1'), Config::get('redisPort', 6379), 3600);
            $self::$pid = getmypid();
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
