<?php

namespace MilesChou\Schemarkdown;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MilesChou\Schemarkdown\Console\GenerateCommand;

class SchemarkdownServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->registerViews();
        $this->registerCommand();
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/templates', 'schemarkdown');
    }

    private function registerCommand(): void
    {
        $this->app->singleton('command.schemarkdown', function () {
            return new GenerateCommand($this->app, 'schemarkdown');
        });

        $this->commands(['command.schemarkdown']);
    }

    public function provides()
    {
        return ['command.schemarkdown'];
    }
}
