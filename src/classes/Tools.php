<?php

namespace Project\Base;

class Tools
{
    public static function forkMe($numChildrenPerSecond = 1, $duration = 60):bool
    {
        $usleep = max(1, floor(1000000 / $numChildrenPerSecond));
        $doneAt = microtime() + ($duration * 1000000);
        $childrenCount = 0;
        $maxChildren = $numChildrenPerSecond * $duration;

        $lastRun = microtime();

        while (microtime() < $doneAt && $childrenCount < $maxChildren) {
            if (pcntl_fork()) return true;
            $sleep = floor(($lastRun + $usleep) - microtime());
            usleep($sleep);
            $childrenCount++;
        }
        return false;
    }

    public static function fetchJSON($url)
    {
        $response = self::curl($url);
        $raw = $response['result'];
        $json = json_decode($raw, true);
        return $json;
    }

    public static function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Project Base Curl Fetcher");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return ['result' => $body, 'httpCode' => $httpCode];
    }
}
