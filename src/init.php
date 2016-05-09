<?php

require_once __DIR__ . "/config.php";

if (php_sapi_name() !== 'cli')
{
    require_once __DIR__ . "/jade.php";

    require_once __DIR__ . "/slim.php";
}
