<?php

namespace App\System;

use Illuminate\Database\Capsule\Manager as Capsule;
trait Database
{
    private static $db = null;
    public static function useDatabase(): ?Capsule
    {
        if (self::$dbStatus) {
            return self::$db;
        }

        self::$dbStatus = true;

        $database = config('database');
        $CONNECTION = $database['default'];
        $config = $database['connections'][$CONNECTION];

        $db = new Capsule;
        $db->addConnection($config);
        $db->setAsGlobal();
        $db->bootEloquent();
        $db::connection()->getPdo()->query('SELECT 1');

        self::$db = $db;
        return $db;
    }


}