<?php

namespace MilesChou\Schemarkdown\Console\Concerns;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use RuntimeException;

trait InitializeConnection
{
    public function initializeConnection(Container $container, string $configPath, ?string $database = null): void
    {
        $connections = $this->resolveConfigFile($configPath);

        if ($database && $this->databaseInConfig($database, $connections)) {
            $connections = Arr::only($connections, $database);
        }

        $container['config']['database.connections'] = $connections;

        // Connect specified database
        foreach ($connections as $name => $database) {
            $container->make(DatabaseManager::class)
                ->connection($name);
        }
    }

    /**
     * @param string $configPath
     * @return array
     */
    private function resolveConfigFile(string $configPath): array
    {
        if (!$configPath) {
            $configPath = 'config/database.php';
        }

        $config = require $this->formatPath($configPath);

        if (!isset($config['connections'])) {
            throw new RuntimeException("The key 'connections' is not set in config file");
        }

        $connections = $config['connections'];

        if (!is_array($connections)) {
            throw new RuntimeException('Connections config is not an array');
        }

        return $connections;
    }

    private function databaseInConfig(string $database, array $connections): bool
    {
        if (!array_key_exists($database, $connections)) {
            throw new RuntimeException('Specify connection is not in config file');
        }

        return true;
    }
}
