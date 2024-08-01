<?php

namespace App\Support;

class Config {
    private static array $config = [];

    /**
     * Retrieves a configuration value.
     *
     * @param string $key The key of the configuration value.
     * @param mixed|null $default The default value if the key is not found.
     * @return mixed The configuration value or the default value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!isset(self::$config[$key]))
        {
            if (file_exists(app_path("config/$key.php")))
            {
                self::$config[basename($key)] = include app_path("config/$key.php");
            }
        }


        if (isset(self::$config[$key]))
        {
            return self::$config[$key];
        }
        elseif (str_contains($key, '.'))
        {
            $key = explode('.', $key);
            if (isset($key[1]))
            {
                $before = $key[0];
                $after = $key[1];


                if (isset(self::$config[$before]))
                {
                    return self::$config[$before][$after];
                }

            }

            return $default;
        }
        else
        {
            return $default;
        }

    }
}
