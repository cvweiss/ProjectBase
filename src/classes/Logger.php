<?php

namespace cvweiss\projectbase;

use \Monolog\Logger as MonoLogger;
use \Monolog\Handler\StreamHandler;

class Logger
{
    private static $logger;

    protected static function getLogger()
    {
        if (self::$logger == null)
        {
            self::$logger = new MonoLogger('projectbase');
            self::$logger->pushHandler(new MongoLogger());
        }
        return self::$logger;
    }

    public static function debug($string)
    {
        return self::getLogger()->debug($string);
    }

    public static function info($string)
    {
        return self::getLogger()->info($string);
    }

    public static function notice($string)
    {
        return self::getLogger()->notice($string);
    }
}
