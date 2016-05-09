<?php

namespace Project\Supply;

class Mongo
{
    private static $manager = null;
    private static $database = null;

    protected static function getConn()
    {
        $server = Config::get("mongo_server", "127.0.0.1");
        $port   = (int) Config::get("mongo_port", 27017);
        self::$database = Config::get("mongo_db", "projectsupply");

        if (self::$manager == null) self::$manager = new \MongoDB\Driver\Manager("mongodb://$server:$port");

        return self::$manager;
    }

    public static function find(string $collection, array $query = [], array $sort = null, int $limit = null):array
    {
        $options = [];
        if ($sort != null) $options['sort'] = $sort;
        if ($limit != null) $options['limit'] = $limit;

        $query = new \MongoDB\Driver\Query($query, $options);
        $cursor = self::getConn()->executeQuery(self::$database . ".$collection", $query);

        $r = $cursor->toArray();
        $result = [];

        foreach ($r as $row)
        {
            $result[] = new MongoDoc($collection, $row);
        }

        return $result;
    }

    public static function insert($collection, $doc)
    {
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => true]);  
        $bulk->insert($doc);
        return self::getConn()->executeBulkWrite(self::$database . ".$collection", $bulk);
    }

    public static function update($collection, $id, $updates)
    {
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => true]);  
        $bulk->update(['_id' => $id], ['$set' => $updates]);
        return self::getConn()->executeBulkWrite(self::$database . ".$collection", $bulk);
    }

    public static function delete($collection, $id)
    {
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => true]);  
        $bulk->delete(['_id' => $id]);
        return self::getConn()->executeBulkWrite(self::$database . ".$collection", $bulk);
    }
}
