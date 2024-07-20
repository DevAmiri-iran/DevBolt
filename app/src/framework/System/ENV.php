<?php

namespace App\System;

use Dotenv\Dotenv;

trait ENV
{
    public static bool $env_exit = false;
    protected static function start_env(): self
    {
        if (file_exists(base_path('.env')))
        {
            $dotenv = Dotenv::createImmutable(ROOT);
            $dotenv->load();
            self::$env_exit = true;
        }

        return new self();
    }
}