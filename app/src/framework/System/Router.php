<?php

namespace App\System;

use App\Route;
use Exception;

trait Router
{
    /**
     * Renders the appropriate route file based on the request method.
     *
     * @throws Exception
     */
    public static function render(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            require_once app_path('routes\\api.php');
        } else {
            require_once app_path('routes\\web.php');
        }
        Route::run();
    }
}
