<?php

namespace MilesChou\Schemarkdown;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;
use MilesChou\Schemarkdown\Console\SchemaMarkdownCommand;
use MilesChou\Schemarkdown\Console\SchemaModelCommand;
use MilesChou\Schemarkdown\Engines\TemplateEngine;

class SchemarkdownServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerViews();
        $this->registerCommand();
    }

    private function registerViews(): void
    {
        $this->app->afterResolving('view', function (Factory $view) {
            $view->addExtension('txt', 'text', function () {
                return $this->app->make(TemplateEngine::class);
            });
        });

        // Notice: must to register package before load views.
        // See https://github.com/laravel/framework/compare/v6.9.0...v6.10.0#diff-bf561b2572e934630e46ff6b69a2e3e359e216f7690fad6ac9b8acaa48502fb4L90-R96
        $this->loadViewsFrom(__DIR__ . '/templates', 'schemarkdown');
    }

    private function registerCommand(): void
    {
        $this->app->singleton('command.schemarkdown.markdown', SchemaMarkdownCommand::class);
        $this->app->singleton('command.schemarkdown.model', SchemaModelCommand::class);

        $this->commands([
            'command.schemarkdown.markdown',
            'command.schemarkdown.model',
        ]);
    }

    public function provides()
    {
        return [
            'command.schemarkdown.markdown',
            'command.schemarkdown.model',
        ];
    }
}
