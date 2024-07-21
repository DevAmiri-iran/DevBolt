<?php

namespace App\System;

use App\System;
use Whoops\Run;

trait Debugger
{
    /**
     * Registers the error debugger with a custom error handler.
     *
     * @return Debugger|System Returns an instance of the class using this trait.
     */
    protected static function register_debugger(): self
    {
        $whoops = new Run;
        $whoops->pushHandler(new class extends \Whoops\Handler\PrettyPageHandler
        {
            /**
             * Handles the exception and logs the error details.
             *
             * @return int Returns the result of the parent handle method.
             */
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
