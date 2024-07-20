<?php

namespace App\Artisan;

class Migrations
{
    private array $paths = [];
    private string $migration = '';

    public function __construct($migration)
    {
        $this->migration = $migration;

        if (empty($this->paths)) {
            $migrationsPath = app_path('database\\migrations');
            $migrations = glob($migrationsPath . '/*.php');

            foreach (array_reverse($migrations) as $migration) {
                $class = basename($migration, '.php');
                $this->paths[$class] = $migration;
            }
        }


    }

    public function up(): void
    {
        $migration = $this->migration;
        $this->migration = '';

        if ($migration == '*') {
            foreach ($this->paths as $class => $path) {
                $migration = require_once $path;
                $migration->up();
            }
        } else {
            if (isset($this->paths[$migration])) {
                $migration = require_once $this->paths[$migration];
                $migration->up();
            } else {
                throw new \Exception("Migration file not found: " . app_path("database\\migrations\\$migration.php"));
            }
        }
    }

    public function down(): void
    {
        $migration = $this->migration;
        $this->migration = '';

        if ($migration == '*') {
            foreach ($this->paths as $class => $path) {
                require_once $path;
                (new $class)->down();
            }
        } else {
            if (isset($this->paths[$migration])) {
                $migration = require_once $this->paths[$migration];
                $migration->down();
            } else {
                throw new \Exception("Migration file not found: " . app_path("database\\migrations\\$migration.php"));
            }
        }
    }
}
