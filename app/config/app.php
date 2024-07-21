<?php

return [

    // Application name
    'name'      => env('APP_NAME', 'DevBolt'),

    // Debug mode
    'debug'     => (bool) env('APP_DEBUG', true),

    // Application URL
    'url'       => env('APP_URL'),

    // Timezone
    'timezone'  => env('APP_TIMEZONE', 'UTC'),

    // Locale
    'locale'    => env('APP_LOCALE', 'en'),

    // Encryption cipher and key
    'cipher'    => 'chacha20',
    'key'       => env('APP_KEY'),

    // Error pages
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
