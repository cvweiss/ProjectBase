<?php

namespace Project\Base\Cron;

// Load Composer
require __DIR__ . '/../vendor/autoload.php';

$numChildrenPerSecond = \Project\Base\Config::get('numJobsPerSecond', 1); // 1 is the default value
$usleep = floor(1000000 / $numChildrenPerSecond);

$time = time();

while (time() < ($time + 60)) {
    if (pcntl_fork()) break;
    usleep($usleep); // sleep...
}

Job::doJob();
