<?php

namespace MilesChou\Schemarkdown\Console\Concerns;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use RuntimeException;

trait InitializeConnection
{
    public function initializeConnection(Container $container, string $configFile, ?string $connection = null): void
    {
        if (!$configFile) {
            $configFile = 'config/database.php';
        }

        $connections = $this->resolveConfigFile($this->formatPath($configFile));

        if ($connection && $this->connectionInConfig($connection, $connections)) {
            $connections = Arr::only($connections, $connection);
        }

        $container['config']['database.connections'] = $connections;

        // Connect specified database
        foreach ($connections as $name => $connection) {
            $container->make(DatabaseManager::class)
                ->connection($name);
        }
    }

    /**
     * @param string $configFile
     * @return array
     */
    private function resolveConfigFile(string $configFile): array
    {
        $config = require $configFile;

        if (!isset($config['connections'])) {
            throw new RuntimeException("The key 'connections' is not set in config file");
        }

        $connections = $config['connections'];

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
