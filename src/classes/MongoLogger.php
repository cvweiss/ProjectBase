<?php

namespace Project\Base;

use \Monolog\Handler\AbstractProcessingHandler;

class MongoLogger extends AbstractProcessingHandler
{
    protected function write(array $record)
    {
        unset($record["formatted"]);
        $dttm = new \MongoDB\BSON\UTCDateTime($record['datetime']->getTimestamp());
        $record['dttm'] = $dttm;
        
        Mongo::insert("log", $record);
    }
}
