<?php

namespace cvweiss\projectbase;

interface CronJob
{
    public function getCron():string;

    public function execute(array $params);
}
