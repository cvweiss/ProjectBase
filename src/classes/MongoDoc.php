<?php

namespace Project\Supply;

class MongoDoc
{
    private $collection = null;
    private $data = null;
    private $updates = [];

    public function __construct($collection, $row)
    {
        $this->collection = $collection;
        $this->data = (array) $row;
    }

    public function get($field)
    {
        return $this->data[$field];
    }

    public function set($field, $value)
    {
        $this->data[$field] = $value;
        $this->updates[$field] = $value;
    }

    public function save()
    {
        $return = null;
        if (isset($this->data['_id'])) $return = Mongo::update($this->collection, $this->data['_id'], $this->updates);
        else $return = Mongo::insert($this->collection, $this->data);
        $this->updates = [];

        return (count($return->getWriteErrors()) == 0);
    }

    public function delete()
    {
        $return = Mongo::delete($this->collection, $this->data['_id']);
        unset($this->data['_id']);
        return (count($return->getWriteErrors()) == 0);
    }

    public function __set($foo, $bar)
    {
        throw new \Exception("use set(\$field, \$value)");
    }
}
