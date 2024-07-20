<?php

namespace App;

class View
{
    private static Blade $blade;
    private static string $views;
    private static string $cache;
    private static string $newViews = '';

    public static function init($views, $cache): View
    {
        self::$views = $views;
        self::$cache = $cache;
        return new self();
    }

    private static function blade(): void
    {
        if (self::$newViews !== '')
        {
            self::$views = self::$newViews;
            self::$newViews = '';
        }

        $blade = new Blade(self::$views, self::$cache, Blade::MODE_AUTO);
        $blade->setCompiledExtension('.php');
        $blade->setOptimize(!env('APP_DEBUG'));
        self::$blade = $blade;
    }
    public static function setViews($path): View
    {
        self::$newViews = $path . '\\resources\\views\\';
        return new self();
    }

    /**
     * @throws \Exception
     */
    public static function render($view, $data = []): View
    {
        self::blade();

        $viewPath = self::$views . "\\$view";
        if (file_exists($viewPath . '.blade.php')) {
            echo self::$blade->run(str_replace('/', '.', $view), $data);
        } elseif (file_exists($viewPath . '.php')) {
            extract($data);
            require $viewPath . '.php';
        } else {
            throw new \Exception("View file not found: " . $viewPath);
        }
        return new self();
    }
}
