<?php

namespace MilesChou\Schemarkdown;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MilesChou\Schemarkdown\Listeners\InitializeCommandOptions;
use MilesChou\Schemarkdown\Listeners\InitializeConnection;

/**
 * Need initialize connection when build on scratch application
 */
class InitializeConnectionBootstrapper extends ServiceProvider
{
    public function boot()
    {
        Event::listen(ArtisanStarting::class, InitializeCommandOptions::class);
        Event::listen(CommandStarting::class, InitializeConnection::class);
    }
}
