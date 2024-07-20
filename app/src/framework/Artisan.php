<?php

namespace App;

use App\Artisan\Migrations;
use App\Artisan\Factories;
class Artisan
{
    public function Migrations($migration): Migrations
    {
        if (System::$dbStatus)
            return new Migrations($migration);
        else
            throw new \Exception("The database is not activated");
    }

    public function Factory($factory): \Illuminate\Database\Eloquent\Factories\Factory
    {
        if (System::$dbStatus)
            return (new Factories())->run($factory);
        else
            throw new \Exception("The database is not activated");
    }
}