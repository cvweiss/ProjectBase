<?php

namespace cvweiss\projectbase;

use MongoDB\Driver\BulkWrite;

class MongoDoc
{
    private $collection = null;
    private $data = null;
    private $updates = [];

    public function __construct(string $collection, array $row = null)
    {
        $this->collection = $collection;
        $this->data = $row === null ? [] : $row;
    }

    public function get(string $field)
    {
        return $this->data[$field] ?? null;
    }

    public function getAll()
    {
        return $this->data;
    }

    public function set(string $field, $value):MongoDoc
    {
        $this->data[$field] = $value;
        $this->updates[$field] = $value;
        return $this;
    }

    public function setAll(array $params):MongoDoc
    {
        foreach ($params as $key=>$value) {
            $this->set($key, $value);
        }
        return $this;
    }

    public function save(BulkWrite $bulk = null):bool
    {
        $return = isset($this->data['_id']) ? $this->update($bulk) : $this->insert($bulk);
        $this->updates = [];

        return $return;
    }

    protected function getBulkWriter(BulkWrite $bulk = null):BulkWrite
    {
        return $bulk ?? new BulkWrite(['ordered' => true]);
    }

    protected function doCommit(BulkWrite $bulk, bool $doCommit):bool
    {
        if ($doCommit !== false) {
            $return = Mongo::get()->executeBulkWrite($this->collection, $bulk);
            return  (count($return->getWriteErrors()) == 0);
        }
        return false;
    }

    protected function insert(BulkWrite $bulk = null):bool
    {
        $commit = $bulk === null;
        $bulk = $this->getBulkWriter($bulk);

        $id = $bulk->insert($this->data);
        $this->data['_id'] = $id;

        return $this->doCommit($bulk, $commit);
    }

    protected function update(BulkWrite $bulk = null):bool
    {   
        // If we have nothing to update then move along
        if (sizeof($this->updates) == 0) return true;

        $commit = $bulk === null;
        $bulk = $this->getBulkWriter($bulk);

        $bulk->update(['_id' => $this->data['_id']], ['$set' => $this->updates]);

        return $this->doCommit($bulk, $commit);
    }

    public function delete(BulkWrite $bulk = null):bool
    {
        $commit = $bulk === null;
        $bulk = $this->getBulkWriter($bulk);

        $bulk->delete(['_id' => $this->data['_id']]);

        return $this->doCommit($bulk, $commit);
    }

    public function __set($foo, $bar)
    {
        throw new \Exception("unable to set $foo to $bar; use set(\$field, \$value)");
    }
}

