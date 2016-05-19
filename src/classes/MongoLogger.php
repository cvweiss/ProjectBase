<?php

namespace Project\Base;

use \Monolog\Handler\AbstractProcessingHandler;

class MongoLogger extends AbstractProcessingHandler
{
    protected function write(array $record)
    {
        unset($record["formatted"]);
        $dttm = new \MongoDB\BSON\UTCDateTime($record['datetime']->getTimestamp() * 1000);
        $record['dttm'] = $dttm;
        
        M::get()->log->insertOne($record);
    }
}
