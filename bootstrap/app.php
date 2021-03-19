<?php

use Illuminate\Console\Application as IlluminateApplication;
use LaravelBridge\Scratch\Application as LaravelBridge;
use MilesChou\Codegener\CodegenerServiceProvider;
use MilesChou\Schemarkdown\Console\SchemaMarkdownCommand;
use MilesChou\Schemarkdown\SchemarkdownServiceProvider;
use org\bovigo\vfs\vfsStream;

require dirname(__DIR__) . '/vendor/autoload.php';

return (static function () {
    $container = (new LaravelBridge())
        ->setupViewCompiledPath(vfsStream::setup('view')->url())
        ->setupProvider(CodegenerServiceProvider::class)
        ->setupProvider(SchemarkdownServiceProvider::class)
        ->withFacades()
        ->bootstrap();

    $app = new IlluminateApplication($container, $container->make('events'), 'dev-master');
    $app->add(new SchemaMarkdownCommand());

    return $app;
})();
