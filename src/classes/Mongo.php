<?php

namespace Project\Base;

class Mongo
{
    private static $pid = null;
    private static $instance = null;

    private $manager = null;
    private $database = null;

    public static function get($server = null, $port = null, $database = null)
    {
        if (self::$instance == null || self::$pid != getmypid()) {
            $server = $server ?? Config::get("mongo_server", "127.0.0.1");
            $port   = $port ?? Config::get("mongo_port", 27017);
            $database = $database ?? Config::get("mongo_db", "projectsupply");

            $manager = new \MongoDB\Driver\Manager("mongodb://$server:$port");

            self::$instance = new Mongo($manager, $database);
            self::$pid = getmypid();
        }

        return self::$instance;
    }

    protected function __construct($manager, $database)
    {
        $this->manager = $manager;
        $this->database = $database;
    }

    public function findDoc(string $collection, array $query = [], array $sort = null)
    {
        $result = $this->find($collection, $query, $sort, 1);
        return sizeof($result) > 0 ? $result[0] : null;
    }

    public function find(string $collection, array $query = [], array $sort = null, int $limit = 0):array
    {
        $options = [];
        if ($sort != null) $options['sort'] = $sort;
        if ($limit != 0) $options['limit'] = $limit;

        $query = new \MongoDB\Driver\Query($query, $options);
        $cursor = $this->manager->executeQuery($this->database . ".$collection", $query);

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

    public function executeBulkWrite($collection, $bulk)
    {
        return $this->manager->executeBulkWrite($this->database . ".$collection", $bulk);
    }
}
