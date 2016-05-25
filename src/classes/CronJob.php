<?php

namespace Project\Base;

interface CronJob
{
    public function getCron():string;

    public function execute(array $params);
}
