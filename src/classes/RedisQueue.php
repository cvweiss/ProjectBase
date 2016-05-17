<?php

namespace Project\Base;

/*
    Brought over from zkillboard/zkillboard
*/
class RedisQueue
{
    private $queueName = null;

    public function __construct($queueName)
    {
        $redis = Redis::getRedis();

        $this->queueName = $queueName;
        $redis->sadd('queues', $queueName);
    }

    public function push($value)
    {
        $redis = Redis::getRedis();

        $redis->rPush($this->queueName, serialize($value));
    }

    public function pop()
    {
        $redis = Redis::getRedis();

        $array = $redis->blPop($this->queueName, 1);
        if (sizeof($array) == 0) {
            return;
        }

        return unserialize($array[1]);
    }

    public function clear()
    {
        $redis = Redis::getRedis();

        $redis->del($this->queueName);
    }
}
