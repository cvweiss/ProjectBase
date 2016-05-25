<?php

namespace Project\Base;

/*
    Brought over from zkillboard/zkillboard
*/
class RedisQueue
{
    private $queueName;
    private $blockTime;

    public function __construct(string $queueName, int $blockTime = 1)
    {
        $this->queueName = $queueName;
        $this->blockTime = $blockTime;
    }

    public function push($value)
    {
        $redis = Redis::getRedis();

        $redis->rPush($this->queueName, serialize($value));
    }

    public function pop()
    {
        $redis = Redis::getRedis();

        $array = $redis->blPop($this->queueName, $this->blockTime);
        if (sizeof($array) == 0) {
            return null;
        }

        return unserialize($array[1]);
    }

    public function clear()
    {
        $redis = Redis::getRedis();

        $redis->del($this->queueName);
    }
}
