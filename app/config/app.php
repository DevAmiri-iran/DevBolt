<?php

return [

    'name'      => env('APP_NAME', 'DevBolt'),

    'debug'     => (bool) env('APP_DEBUG', true),

    'url'       => env('APP_URL'),

    'timezone'  => env('APP_TIMEZONE', 'UTC'),

    'locale'    => env('APP_LOCALE', 'en'),

    'cipher'    => 'chacha20',
    'key'       => env('APP_KEY'),


    'errors' => [
        '401'   => '',
        '402'   => '',
        '403'   => '',
        '404'   => '',
        '405'   => '',
        '419'   => '',
        '429'   => '',
        '500'   => '',
        '503'   => '',
    ],
];
