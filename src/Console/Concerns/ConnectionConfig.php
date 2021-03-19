<?php

namespace MilesChou\Schemarkdown\Console\Concerns;

use Illuminate\Support\Fluent;
use RuntimeException;

trait ConnectionConfig
{
    /**
     * @param string $configFile
     * @return array
     */
    protected function normalizeConnectionConfig($configFile): array
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
}
