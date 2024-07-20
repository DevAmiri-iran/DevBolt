<?php

namespace App\System;

trait View
{
    protected static function config_view(): self
    {
        \App\View::init(resources_path('views'), resources_path('storage\\cache'));
        return new self();
    }
}