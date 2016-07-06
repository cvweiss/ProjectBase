<?php

namespace Project\Base;

class JobQueue
{
    private $redis = null;
    private $queueName = null;
    private $numQueues = 5;
    private $blockTime;

    private $q0 = null;
    private $q1 = null;
    private $q2 = null;
    private $q3 = null;
    private $q4 = null;

    public function __construct(\Redis $redis, string $queueName = 'queue', int $blockTime = 1)
    {
        $this->redis = $redis;
        $this->queueName = $queueName;
        $this->blockTime = $blockTime;

        $this->q0 = $queueName . "0";
        $this->q1 = $queueName . "1";
        $this->q2 = $queueName . "2";
        $this->q3 = $queueName . "3";
        $this->q4 = $queueName . "4";
    }

    public function push($value, int $priority = 0)
    {
        if ($priority < 0 || $priority >= $this->numQueues) {
            throw new \IllegalArgumentException("Invalid priority");
        }

        $this->redis->rPush($this->queueName . $priority, serialize($value));
    }

    public function pop()
    {
        $array = $this->redis->blPop($this->q0, $this->q1, $this->q2, $this->q3, $this->q4, $this->blockTime);
        if (sizeof($array) == 0) {
            return null;
        }

        return unserialize($array[1]);
    }
}
