<?php

namespace App\Support;

class Config {
    private static $config = [];

    public static function load($configDir) {
        foreach (glob($configDir . '/*.php') as $file) {
            $key = basename($file, '.php');
            self::$config[$key] = require $file;
        }
    }

    public static function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
