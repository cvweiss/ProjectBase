<?php

namespace Project\Base;

class JobQueue
{
    private $numQueues = 5;
    private $blockTime;

    public function __construct(int $blockTime = 1)
    {
        $this->blockTime = $blockTime;
    }

    public function push($value, int $priority = 0)
    {
        if ($priority < 0 || $priority > $this->numQueues) throw new \IllegalArgumentException("Invalid priority");
        $redis = Redis::getRedis();

        $redis->rPush("queue" . $priority, serialize($value));
    }

    public function pop()
    {
        $redis = Redis::getRedis();

        $array = $redis->blPop('queue0', 'queue1', 'queue2', 'queue3', 'queue4', 'queue5', $this->blockTime);
        if (sizeof($array) == 0) {
            return null;
        }

        return unserialize($array[1]);
    }
}
