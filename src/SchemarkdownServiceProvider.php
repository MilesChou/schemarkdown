<?php

namespace MilesChou\Schemarkdown;

use Illuminate\Support\ServiceProvider;

class SchemarkdownServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/templates', 'schemarkdown');
    }
}
