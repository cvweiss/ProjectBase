#!/usr/bin/php
<?php

namespace Project\Base\Cron;

use Project\Base\Tools;

// Load Composer
require __DIR__ . '/../vendor/autoload.php';

$numChildrenPerSecond = \Project\Base\Config::get('numJobsPerSecond', 1); // 1 is the default value

if (Tools::forkMe($numChildrenPerSecond, 60)) Job::doJob();
