<?php

namespace Project\Base;

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
        $redis = Redis::getRedis();

        return $redis->get("sess:$id");
    }

    public function write($id, $data)
    {
        $sessionTimeout = (int) Config::get("session_timeout", 3600);
        $redis = Redis::getRedis();

        $redis->setex("sess:$id", $sessionTimeout, $data);

        return true;
    }

    public function destroy($id)
    {
        $redis = Redis::getRedis();

        $redis->del("sess:$id");

        return true;
    }

    public function gc($maxlifetime)
    {
        return true;
    }
}
