<?php

namespace Project\Base;

class Mongo
{
    private static $pid = null;
    private static $manager = null;
    private static $database = null;

    protected static function getConn()
    {
        if (self::$manager == null || self::$pid != getmypid()) {
            $server = Config::get("mongo_server", "127.0.0.1");
            $port   = (int) Config::get("mongo_port", 27017);
            self::$database = Config::get("mongo_db", "projectsupply");

            self::$manager = new \MongoDB\Driver\Manager("mongodb://$server:$port");
            self::$pid = getmypid();
        }

        return self::$manager;
    }

    public static function findDoc(string $collection, array $query = [], array $sort = null)
    {
        $result = self::find($collection, $query, $sort, 1);
        return sizeof($result) > 0 ? $result[0] : null;
    }

    public static function find(string $collection, array $query = [], array $sort = null, int $limit = 0):array
    {
        $options = [];
        if ($sort != null) $options['sort'] = $sort;
        if ($limit != 0) $options['limit'] = $limit;

        $query = new \MongoDB\Driver\Query($query, $options);
        $cursor = self::getConn()->executeQuery(self::$database . ".$collection", $query);

        $r = $cursor->toArray();
        array_reverse($r);
        $result = [];

        while (sizeof($r) > 0)
        {
            $row = (array) array_pop($r);
            $result[] = new MongoDoc($collection, $row);
        }

        return $result;
    }

    public static function executeBulkWrite($collection, $bulk)
    {
        return self::getConn()->executeBulkWrite(self::$database . ".$collection", $bulk);
    }
}
