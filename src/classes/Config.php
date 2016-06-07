<?php

namespace Project\Base;

class Config
{
    private static $instance = null;

    public static function getInstance()
    {
        self::$instance = self::$instance ?? new Config();
        return self::$instance;
    }

    private $settings = [];

    public function get(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function getAll():array
    {
        return $this->settings;
    }

    public function set(string $key, $value, $overRide = false)
    {
        if ($overRide === false && isset($this->settings[$key])) {
            throw new \Exception("$key already set, cannot overwrite");
        }
        $this->settings[$key] = $value;
    }

    public function setAll(array $keys)
    {
        foreach ($keys as $key=>$value) {
            $this->set($key, $value);
        }
    }
}
