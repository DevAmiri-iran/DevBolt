<?php

namespace App\System;

trait FileMaker
{
    private static array $item = [];

    private static function Apache(): void
    {
        self::$item[base_path('.htaccess')] = <<<HTACCESS
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_URI}::$1 ^(.*?/)(.*)::\\2$
    RewriteRule ^(.*)$ - [E=BASE:%1]

    RewriteCond %{HTTP_HOST} !^$
    RewriteCond %{HTTP_HOST} ^(.+)$
    RewriteRule ^(.*)$ %{ENV:BASE}/public/$1 [L]

    RewriteRule ^$ %{ENV:BASE}/public/ [L]

    RewriteRule ^(.*)$ %{ENV:BASE}/public/$1 [L]

    <IfModule mime_module>
        AddHandler application/x-httpd-ea-php82 .php .php8 .phtml
    </IfModule>
</IfModule>
HTACCESS;

        self::$item[public_path('.htaccess')] = <<<HTACCESS
<IfModule mod_rewrite.c>
    RewriteEngine On

    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    Header set Access-Control-Allow-Origin "*"

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^(.*)$ index.php?route=/$1 [QSA,L,B]
</IfModule>
HTACCESS;
    }

    private static function env(): void
    {
        $url = getCurrentUrl();
        $key = str_replace('=', '', base64_encode(random())) . '5' . str_replace('=', '', base64_encode(random()));
        self::$item[base_path('.env')] =
"APP_NAME=DevBolt
APP_URL=$url
APP_KEY=$key
APP_DEBUG=". env('APP_DEBUG', 'true') ."

DB_DRIVER=". env('DB_DRIVER', 'mysql') ."
DB_HOST=". env('DB_HOST', '127.0.0.1') ."
DB_DATABASE=". env('DB_DATABASE', '') ."
DB_USERNAME=". env('DB_USERNAME', '') ."
DB_PASSWORD=". env('DB_PASSWORD', '') ."
DB_CHARSET=". env('DB_CHARSET', 'utf8') ."
DB_COLLATION=". env('DB_COLLATION', 'utf8_unicode_ci') ."
DB_PREFIX=". env('DB_PREFIX', '') ."

SESSION_NAME=". env('SESSION_NAME', 'DevBolt') ."
SESSION_DRIVER=". env('SESSION_DRIVER', 'files') ."
SESSION_LIFETIME=". env('SESSION_LIFETIME', '43200') ."
SESSION_PATH=". env('SESSION_PATH', '/') ."
SESSION_SAVE_PATH=". env('SESSION_SAVE_PATH', '/tmp/sessions') ."
SESSION_DOMAIN=". env('SESSION_DOMAIN', 'null') ."
SESSION_SECURE=". env('SESSION_SECURE', 'falsfalsee') ."
SESSION_HTTPONLY=". env('SESSION_HTTPONLY', 'true') ."

MAIL_MAILER=". env('MAIL_MAILER', 'log') ."
MAIL_HOST=". env('MAIL_HOST', '127.0.0.1') ."
MAIL_PORT=". env('MAIL_PORT', '2525') ."
MAIL_USERNAME=". env('MAIL_USERNAME', 'null') ."
MAIL_PASSWORD=". env('MAIL_PASSWORD', 'null') ."
MAIL_ENCRYPTION=". env('MAIL_ENCRYPTION', 'null') ."
MAIL_LOG_CHANNEL=". env('MAIL_LOG_CHANNEL', 'storage/logs/email/') .'
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"';
    }

    private static function handel(): void
    {
        self::env();

        if (str_contains($_SERVER['SERVER_SOFTWARE'], 'Apache')) {
            self::Apache();
        } elseif (str_contains($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed')) {
            self::Apache();
        }
    }
    public static function StartFileMaker(): void
    {
        self::handel();
        foreach (self::$item as $path => $value) {
            file_put_contents($path, $value);
        }
    }
}
