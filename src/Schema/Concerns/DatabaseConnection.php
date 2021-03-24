<?php

namespace MilesChou\Schemarkdown\Schema\Concerns;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\Connection;

/**
 * Transfer to SchemaManager and register type mapping
 */
trait DatabaseConnection
{
    /**
     * @param Connection $connection
     * @return AbstractSchemaManager
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function resolveSchemaManger(Connection $connection): AbstractSchemaManager
    {
        $doctrineConnection = $connection->getDoctrineConnection();

        $databasePlatform = $doctrineConnection->getDatabasePlatform();
        $databasePlatform->registerDoctrineTypeMapping('json', 'text');
        $databasePlatform->registerDoctrineTypeMapping('jsonb', 'text');
        $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
        $databasePlatform->registerDoctrineTypeMapping('bit', 'boolean');

        // Postgres types
        $databasePlatform->registerDoctrineTypeMapping('_text', 'text');
        $databasePlatform->registerDoctrineTypeMapping('_int4', 'integer');
        $databasePlatform->registerDoctrineTypeMapping('_numeric', 'float');
        $databasePlatform->registerDoctrineTypeMapping('cidr', 'string');
        $databasePlatform->registerDoctrineTypeMapping('inet', 'string');

        return $doctrineConnection->getSchemaManager();
    }
}
