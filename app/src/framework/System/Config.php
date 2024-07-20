<?php

namespace App\System;

trait Config
{
    protected static function config_loader(): self
    {
        \App\Support\Config::load(app_path('config'));
        return new self();
    }
}