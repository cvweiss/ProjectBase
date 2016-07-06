<?php

namespace cvweiss\projectbase;

use MongoDB\Driver\BulkWrite;

class MongoCollection
{
    private $collection = null;
    private $mongo = null;

    public function __construct($collection, $mongo = null)
    {
        $this->collection = $collection;
        $this->mongo = $mongo;
    }

    protected function getManager()
    {
        $this->mongo = $this->mongo ?? Mongo::get();
        return $this->mongo->getManager();
    }

    protected function getDatabase()
    {   
        $this->mongo = $this->mongo ?? Mongo::get();
        return $this->mongo->getDatabase();
    }

    public function count($query = [])
    {
        $command = new \MongoDB\Driver\Command(['count' => $this->collection]);

        $cursor = $this->getManager()->executeCommand($this->getDatabase(), $command);
        $results = $cursor->toArray()[0];

        return (int) $results->n ?? 0;
    }

    public function saveAll(array $docs):bool
    {
        $bulk = new BulkWrite(['ordered' => true]);

        foreach ($docs as $doc) {
            $doc->save($bulk);
        }

        $return = Mongo::get()->executeBulkWrite($this->collection, $bulk);
        return  (count($return->getWriteErrors()) == 0);
    }
}
