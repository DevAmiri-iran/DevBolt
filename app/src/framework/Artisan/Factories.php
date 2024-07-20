<?php

namespace App\Artisan;


use Illuminate\Database\Eloquent\Factories\Factory;

class Factories
{
    public function run($factory): Factory
    {
        $factory = app_path("database\\factories\\$factory.php");
        if (file_exists($factory))
            return require_once $factory;
        else
            throw new \Exception("Factory file not found: " . $factory);
    }
}
