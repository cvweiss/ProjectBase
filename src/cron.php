<?php

namespace Project\Base\Cron;

$numChildrenPerSecond = 1; // 1 is the default value
$usleep = 1000000 / $numChildrenPerSecond;

$time = time();

$first = true;
$pid = null;
while (time() < ($time + 60))
{
    $pid = pcntl_fork();
    if ($pid == 0) break; // We are the child
    $first = false;
    usleep($usleep); // sleep...
}

// Load Composer
require __DIR__ . '/../vendor/autoload.php';

if ($first) Job::addJobs();
Job::doJob();
