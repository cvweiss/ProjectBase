<?php

namespace Project\Base;

class Db
{
    private static $client = null;

    public static function get()
    {
        if (self::$client === null) {
            $server = Config::get("mongo_server", "127.0.0.1");
            $port   = (int) Config::get("mongo_port", 27017);

            self::$client = new \MongoDB\Client("mongodb://$server:$port");
        }

        $database = Config::get("mongo_db", "projectsupply");
        return self::$client->$database;
    }
}
