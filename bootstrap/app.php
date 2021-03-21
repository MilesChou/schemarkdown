<?php

use Illuminate\Console\Application as IlluminateApplication;
use LaravelBridge\Scratch\Application as LaravelBridge;
use MilesChou\Codegener\CodegenerServiceProvider;
use MilesChou\Schemarkdown\Console\SchemaMarkdownCommand;
use MilesChou\Schemarkdown\Console\SchemaModelCommand;
use MilesChou\Schemarkdown\InitializeConnectionBootstrapper;
use MilesChou\Schemarkdown\SchemarkdownServiceProvider;
use org\bovigo\vfs\vfsStream;

require dirname(__DIR__) . '/vendor/autoload.php';

return (static function () {
    $container = (new LaravelBridge())
        ->setupViewCompiledPath(vfsStream::setup('view')->url())
        ->setupProvider(CodegenerServiceProvider::class)
        ->setupProvider(SchemarkdownServiceProvider::class)
        ->setupProvider(InitializeConnectionBootstrapper::class)
        ->withFacades()
        ->bootstrap();

    $app = new IlluminateApplication($container, $container->make('events'), 'dev-master');
    $app->add(new SchemaMarkdownCommand());
    $app->add(new SchemaModelCommand());

    return $app;
})();
