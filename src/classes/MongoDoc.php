<?php

namespace Project\Base;

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
        return $this->data[$field];
    }

    public function getAll()
    {
        return $this->data;
    }

    public function set(string $field, $value)
    {
        $this->data[$field] = $value;
        $this->updates[$field] = $value;
    }

    public function setAll(array $params)
    {
        foreach ($params as $key=>$value) {
            $this->set($key, $value);
        }
    }

    public function save():bool
    {
        $return = isset($this->data['_id']) ? $this->update() : $this->insert();
        $this->updates = [];

        return $return;
    }

    protected function insert():bool
    {   
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => true]);
        $id = $bulk->insert($this->data);
        $this->data['_id'] = $id;
        $return = Mongo::get()->executeBulkWrite($this->collection, $bulk);

        return  (count($return->getWriteErrors()) == 0);
    }

    protected function update():bool
    {   
        // If we have nothing to update then move along
        if (sizeof($this->updates) == 0) return true;

        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => true]);
        $bulk->update(['_id' => $this->data['_id']], ['$set' => $this->updates]);
        $return = Mongo::get()->executeBulkWrite($this->collection, $bulk);

        return (count($return->getWriteErrors()) == 0);
    }

    public function delete():bool
    {   
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => true]);
        $bulk->delete(['_id' => $this->data['_id']]);
        $return = Mongo::get()->executeBulkWrite($this->collection, $bulk);

        return (count($return->getWriteErrors()) == 0);
    }

    public function __set($foo, $bar)
    {
        throw new \Exception("unable to set $foo to $bar; use set(\$field, \$value)");
    }
}
