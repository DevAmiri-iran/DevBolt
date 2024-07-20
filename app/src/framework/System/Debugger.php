<?php

namespace App\System;

use Whoops\Run;

trait Debugger
{
    protected static function register_debugger(): self
    {
        $whoops = new Run;
        $whoops->pushHandler(new class extends \Whoops\Handler\PrettyPageHandler
        {
            public function handle(): int
            {
                $e = $this->getException();

                $log = new \Monolog\Logger('local');
                $log->pushHandler(new \Monolog\Handler\StreamHandler(resources_path('storage\\logs') . '\\' . date('Y.m.d') . '.log', \Monolog\Logger::ERROR));
                $log->error('An error occurred: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);

                $this->setPageTitle("Oops! Something went wrong.");

                if (!env('APP_DEBUG')) {
                    showError(500);
                }
                return parent::handle();
            }
        });
        $whoops->register();
        return new static();
    }
}
