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

    public function set(string $field, $value)
    {
        $this->data[$field] = $value;
        $this->updates[$field] = $value;
    }

    public function save()
    {
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
