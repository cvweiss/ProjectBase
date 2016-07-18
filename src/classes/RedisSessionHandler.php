<?php

namespace cvweiss\projectbase;

class RedisSessionHandler implements \SessionHandlerInterface
{
    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $redis = Redis::get();

        return $redis->get("sess:$id");
    }

    public function write($id, $data)
    {
        $sessionTimeout = (int) Config::getInstance()->get("session_timeout", 3600);
        $redis = Redis::get();

        $redis->setex("sess:$id", $sessionTimeout, $data);

        return true;
    }

    public function destroy($id)
    {
        $redis = Redis::get();

        $redis->del("sess:$id");

        return true;
    }

    public function gc($maxlifetime)
    {
        return true;
    }
}
