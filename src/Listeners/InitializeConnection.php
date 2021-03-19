<?php

namespace MilesChou\Schemarkdown\Listeners;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use LaravelBridge\Scratch\Application;
use MilesChou\Codegener\Traits\Path;
use RuntimeException;

class InitializeConnection
{
    use Path;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var DatabaseManager
     */
    private $database;

    public function __construct(Application $app, DatabaseManager $database)
    {
        $this->app = $app;
        $this->database = $database;
    }

    public function handle(CommandStarting $event): void
    {
        if (!$this->needed($event->command)) {
            return;
        }

        // Load config file
        $configFile = $event->input->getParameterOption('--config-file');

        if (!$configFile) {
            $configFile = 'config/database.php';
        }

        $connections = $this->resolveConfigFile($this->formatPath($configFile));

        // Setup connection config by input config
        $connection = $event->input->getParameterOption('--connection');

        if ($connection && $this->connectionInConfig($connection, $connections)) {
            $connections = Arr::only($connections, $connection);
        }

        $this->app['config']['database.connections'] = $connections;

        // Connect specified database
        foreach ($connections as $name => $connection) {
            $this->database->connection($name);
        }
    }

    private function needed(?string $command): bool
    {
        return in_array($command, [
            'schema:markdown',
            'schema:model',
        ], true);
    }

    /**
     * @param string $configFile
     * @return array
     */
    protected function resolveConfigFile(string $configFile): array
    {
        $config = require $configFile;

        $config = new Fluent($config);

        if (!isset($config['connections'])) {
            throw new RuntimeException("The key 'connections' is not set in config file");
        }

        $connections = $config->get('connections');

        if (!is_array($connections)) {
            throw new RuntimeException('Connections config is not an array');
        }

        return $connections;
    }

    private function connectionInConfig(string $connection, array $connections): bool
    {
        if (!array_key_exists($connection, $connections)) {
            throw new RuntimeException('Specify connection is not in config file');
        }

        return true;
    }
}
