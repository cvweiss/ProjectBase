<?php

namespace Project\Base\Cron;

class Foo extends Job
{

    public function execute()
    {
        echo "Foo!\n";
    }

}
