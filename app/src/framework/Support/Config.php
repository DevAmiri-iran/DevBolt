<?php

namespace App\Support;

class Config {
    private static array $config = [];

    /**
     * Loads configuration files from the specified directory.
     *
     * @param string $configDir The directory containing the configuration files.
     */
    public static function load(string $configDir): void
    {
        foreach (glob($configDir . '/*.php') as $file) {
            $key = basename($file, '.php');
            self::$config[$key] = require $file;
        }
    }

    /**
     * Retrieves a configuration value.
     *
     * @param string $key The key of the configuration value.
     * @param mixed|null $default The default value if the key is not found.
     * @return mixed The configuration value or the default value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
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
