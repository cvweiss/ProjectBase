<?php

namespace cvweiss\projectbase;

require_once __DIR__ . '/config.php';

if (Config::getInstance()->get('debug', true) === true) {
    Setup::prepareProject();
}

if (php_sapi_name() !== 'cli')
{
    require_once __DIR__ . '/session.php';

    require_once __DIR__ . '/jade.php';

    require_once __DIR__ . '/router.php';
}
