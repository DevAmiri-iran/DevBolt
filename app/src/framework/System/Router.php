<?php

namespace App\System;

use App\System;

trait Router
{
    /**
     * @throws \Exception
     */
    public static function render(): void
    {
        if ( $_SERVER['REQUEST_METHOD'] !== 'GET')
            require_once app_path('routes\\api.php');
        else
            require_once app_path('routes\\web.php');
        \App\Route::run();
    }
}