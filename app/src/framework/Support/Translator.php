<?php

namespace App\Support;

class Translator {
    private static array $translations = [];
    private static $locale;

    public static function init() {
        self::$locale = Config::get('app.locale', 'en');
        self::loadTranslations(self::$locale);
    }

    private static function loadTranslations($locale) {
        $langDir = resources_path("lang\\$locale");
        if (is_dir($langDir)) {
            $files = scandir($langDir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $translations = require "$langDir/$file";
                    self::$translations = array_merge(self::$translations, $translations);
                }
            }
        }
    }

    public static function trans($key, $default = null) {
        return self::$translations[$key] ?? $default ?? $key;
    }
}
