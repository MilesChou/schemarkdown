<?php

namespace MilesChou\Schemarkdown\Listeners;

use Illuminate\Console\Events\ArtisanStarting;
use Symfony\Component\Console\Input\InputOption;

class InitializeCommandOptions
{
    public function handle(ArtisanStarting $event): void
    {
        $event->artisan->setName('Schemarkdown');

        $event->artisan->getDefinition()->addOptions([
            new InputOption('--config-file', null, InputOption::VALUE_REQUIRED, 'Config file'),
        ]);
    }
}
