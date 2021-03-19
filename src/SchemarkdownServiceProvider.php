<?php

namespace MilesChou\Schemarkdown;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MilesChou\Schemarkdown\Console\SchemaMarkdownCommand;
use MilesChou\Schemarkdown\Listeners\InitializeCommandOptions;
use MilesChou\Schemarkdown\Listeners\InitializeConnection;

class SchemarkdownServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerViews();
        $this->registerCommand();
    }

    public function boot()
    {
        Event::listen(ArtisanStarting::class, InitializeCommandOptions::class);
        Event::listen(CommandStarting::class, InitializeConnection::class);
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/templates', 'schemarkdown');
    }

    private function registerCommand(): void
    {
        $this->app->singleton('command.schemarkdown', SchemaMarkdownCommand::class);

        $this->commands(['command.schemarkdown']);
    }

    public function provides()
    {
        return ['command.schemarkdown'];
    }
}
