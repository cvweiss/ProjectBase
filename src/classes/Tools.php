<?php

namespace Project\Base;

class Tools
{
    public static function forkMe($numChildrenPerSecond = 1, $duration = 60):bool
    {
        $usleep = max(1, floor(1000000 / $numChildrenPerSecond));
        $time = time();
        $doneAt = microtime() + ($duration * 1000000);
        $childrenCount = 0;
        $maxChildren = $numChildrenPerSecond * $duration;

        $lastRun = microtime();

        while (microtime() < $doneAt && $childrenCount < $maxChildren) {
            if (pcntl_fork()) return true;
            $sleep = floor(($lastRun + $usleep) - microtime());
            usleep($sleep);
            $childrenCount++;
        }
        return false;
    }
}
