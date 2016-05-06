<?php

namespace Project\Supply;

class Config
{
    private static $settings = [];

    public static function get(string $key, $default = null)
    {
        return self::$settings[$key] ?? $default;
    }

    public static function getAll():array
    {
        return self::$settings;
    }

    public static function set(string $key, $value)
    {
        if (isset(self::$settings[$key])) throw new Exception("$key already set, cannot overwrite");
        self::$settings[$key] = $value;
    }

    public static function setAll(array $keys)
    {
        foreach ($keys as $key=>$value) self::set($key, $value);
    }
}
