<?php

use App\Artisan;
use App\Encryption\Crypt;
use App\Encryption\JWT;
use App\Mail\Mailer;
use App\Mail\Message;
use App\Support\Config;
use App\Support\CookieManager;
use App\Support\Option;
use App\Support\Translator;
use App\System;
use App\View;
use Faker\Factory as Faker;
use JetBrains\PhpStorm\NoReturn;

if (!function_exists('view')) {
    function view($viewName, array $data = []): void
    {
        View::render($viewName, $data);
    }
}

if (!function_exists('api'))
{
    function api($view, $data = []): void
    {
        $path = resources_path("api/$view.php");
        if (is_file($path))
        {
            require_once $path;
        }
        else
        {
            throw new \InvalidArgumentException("File ". basename($view) .".php could not be found in directory ". dirname($path));
        }
    }
}

if (!function_exists('trans')) {
    function trans($key, $default = null) {
        return Translator::trans($key, $default);
    }
}

if (!function_exists('__')) {
    function __($key, $default = null) {
        return trans($key, $default);
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        return Config::get($key, $default);
    }
}



if (!function_exists('session')) {
    function session(): \Symfony\Component\HttpFoundation\Session\Session
    {
        return System::start_session();
    }
}


if (!function_exists('cookie')) {
    function cookie(): CookieManager
    {
        return new CookieManager;
    }
}

if (!function_exists('hasXSS')) {
    function hasXSS(array|string $input): bool
    {
        $checkInput = function($value) {
            $blacklist = [
                'sql_injection' => '/\b(SELECT|INSERT|UPDATE|DELETE)\b/i',
                'xss_script' => '/<script\b[^>]*>(.*?)<\/script>/i',
                'xss_img' => '/<img\b[^>]*>/i',
                'php_code' => '/<\?php|<\?/i',
                'alert' => '/\balert\b/i',
                'function' => '/\bfunction\b/i',
                'eval' => '/\beval\b/i',
                'script' => '/\bscript\b/i',
            ];
            foreach ($blacklist as $pattern) {
                if (preg_match($pattern, $value)) {
                    return true;
                }
            }
            return false;
        };

        if (is_array($input)) {
            foreach ($input as $item) {
                if ($checkInput($item)) {
                    return true;
                }
            }
        } else {
            return $checkInput($input);
        }

        return false;
    }
}

if (!function_exists('dd')) {
    #[NoReturn] function dd(...$args): void
    {
        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
        }
        echo '</pre>';
        die(1);
    }
}

if (!function_exists('d')) {
    function d(...$args): void
    {
        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
        }
        echo '</pre>';
    }
}

if (!function_exists('encrypt')) {
    function encrypt($data): string
    {
        return Crypt::encrypt($data);
    }
}

if (!function_exists('decrypt')) {
    function decrypt($data): string
    {
        return Crypt::decrypt($data);
    }
}

if (!function_exists('random')) {
    function random(int $length = 32, bool $letters = true, bool $numbers = true, bool $symbols = true, bool $spaces = false): string
    {
        $characters = '';
        $string = '';

        if ($letters) {
            $characters .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($numbers) {
            $characters .= '0123456789';
        }
        if ($symbols) {
            $characters .= '~!#$%^&*()_-.,<>?/\\{}[]|:;';
        }
        if ($spaces) {
            $characters .= ' ';
        }

        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $string;
    }
}

if (!function_exists('redirect')) {
    #[NoReturn] function redirect(string $url): void
    {
        header('Location: ' . $url);
        echo "<meta http-equiv='refresh' content='0; url={$url}' />";
        echo "<script>window.location.href = '{$url}';</script>";
        exit;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $path = trim($path, '/\\');
        return url("assets/" . $path);
    }
}

if (!function_exists('url')) {
    function url(string $path = '/'): string
    {
        $path = trim($path, '/\\');
        return rtrim($_ENV['APP_URL'], '/') . '/' . $path;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        $session = session();
        if (!$session->has('csrf_token')) {
            $session->set('csrf_token', bin2hex(random_bytes(22)));
        }
        return $session->get('csrf_token');
    }
}

if (!function_exists('getUserIP')) {
    function getUserIP(): string
    {
        $ipSources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];

        foreach ($ipSources as $source) {
            if (!empty($_SERVER[$source])) {
                $ips = explode(',', $_SERVER[$source]);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }
        return 'UNKNOWN';
    }
}

if (!function_exists('getCurrentUrl')) {
    function getCurrentUrl(): string|false
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            return $protocol . "://" . $host . $uri;
        }
        return false;
    }
}

if (!function_exists('back'))
{
    #[NoReturn]
    function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        redirect($referer);
    }
}

if (!function_exists('route'))
{
    function route(string $name, array $params = []): string
    {
        $router = new \App\Route();
        return $router->generateUrl($name, $params);
    }
}

if (!function_exists('showError'))
{
    #[NoReturn] function showError($error, $httpCode = 500): void
    {
        $errors = config('app.errors');

        if (isset($errors[$error])) {
            $view = $errors[$error];
            if ($view == '') {
                $view = "errors" . DIRECTORY_SEPARATOR . $error;
                View::setViews(src_path("framework"));
            }
            http_response_code($httpCode);
            View::render($view);
            exit();
        } else {
            $path = app_path('config\\app.php');
            throw new \InvalidArgumentException("Key $error is not defined in the $path file.");
        }
    }
}

if (!function_exists('base_path'))
{
    function base_path(string $path = ''): string
    {
        $path = trim($path, '/\\');
        $fullPath = ROOT . DIRECTORY_SEPARATOR . $path;
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fullPath);
    }
}

if (!function_exists('app_path'))
{
    function app_path(string $path = ''): string
    {
        $path = trim($path, '/\\');
        return base_path('app' . DIRECTORY_SEPARATOR . $path);
    }
}

if (!function_exists('public_path'))
{
    function public_path(string $path = ''): string
    {
        $path = trim($path, '/\\');
        return base_path('public' . DIRECTORY_SEPARATOR . $path);
    }
}

if (!function_exists('src_path'))
{
    function src_path(string $path = ''): string
    {
        $path = trim($path, '/\\');
        return base_path('app' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $path);
    }
}

if (!function_exists('resources_path'))
{
    function resources_path(string $path = ''): string
    {
        $path = trim($path, '/\\');
        return base_path('resources' . DIRECTORY_SEPARATOR . $path);
    }
}

if (!function_exists('storage_path'))
{
    function storage_path(string $path = ''): string
    {
        $path = trim($path, '/\\');
        return resources_path('storage' . DIRECTORY_SEPARATOR . $path);
    }
}


if (!function_exists('str_slug'))
{
    function str_slug($string, $separator = '-'): string
    {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', $separator, strtolower($string));
        return trim($slug, $separator);
    }
}

if (!function_exists('array_except'))
{
    function array_except($array, $keys)
    {
        foreach ((array)$keys as $key) {
            unset($array[$key]);
        }
        return $array;
    }
}

if (!function_exists('option'))
{
    function option(): Option
    {
        return new Option('settings.json', true);
    }
}

if (!function_exists('JWT'))
{
    function JWT(): JWT
    {
        return new JWT;
    }
}

if (!function_exists('refresh'))
{
    #[NoReturn] function refresh(): void
    {
        redirect(getCurrentUrl());
    }
}

if (!function_exists('migrations'))
{
    function migrations($migration): Artisan\Migrations
    {
        return \Artisan()->Migrations($migration);
    }
}

if (!function_exists('factory'))
{
    function factory($factory): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Artisan()->Factory($factory);
    }
}

if (!function_exists('fake'))
{
    function fake(): \Faker\Generator
    {
        return Faker::create();
    }
}

if (!function_exists('Artisan'))
{
    function Artisan(): Artisan
    {
        return new Artisan();
    }
}

if (!function_exists('Mailer'))
{
    function Mailer(): Mailer
    {
        return new Mailer();
    }
}