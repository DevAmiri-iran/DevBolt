<?php

namespace App\System;

use App\Support\Config;

trait Translator
{

    protected static function Translator_loader(): self
    {
        Config::get('app');
        \App\Support\Translator::init();
        return new self();
    }
}