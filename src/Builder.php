<?php

declare(strict_types=1);

namespace MilesChou\Schemarkdown;

use Doctrine\DBAL\DBALException;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use MilesChou\Schemarkdown\Events\BuildingConnection;
use MilesChou\Schemarkdown\Events\BuildingReadme;
use MilesChou\Schemarkdown\Events\BuildingSchema;
use MilesChou\Schemarkdown\Models\Schema;
use MilesChou\Schemarkdown\Models\Table;
use Psr\Log\LoggerInterface;

class Builder
{
    /**
     * @var array
     */
    private $connections;

    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var Events
     */
    private $events;

    /**
     * @var bool
     */
    private $withConnectionNamespace;

    /**
     * @var View
     */
    private $view;

    /**
     * @param DatabaseManager $databaseManager
     * @param View $view
     * @param Events $events
     * @param array $connections
     */
    public function __construct(DatabaseManager $databaseManager, View $view, Events $events, array $connections)
    {
        $this->databaseManager = $databaseManager;
        $this->view = $view;
        $this->events = $events;
        $this->connections = $connections;
        $this->withConnectionNamespace = count($this->connections) > 1;
    }

    /**
     * @return array [filepath => code]
     * @throws DBALException
     */
    public function build(): iterable
    {
        foreach (array_keys($this->connections) as $connection) {
            $this->events->dispatch(new BuildingConnection($connection));

            yield from $this->buildConnection($connection);
        }
    }

    /**
     * @param string $connection
     * @return mixed
     * @throws DBALException
     */
    private function buildConnection($connection)
    {
        $databaseConnection = $this->databaseManager->connection($connection);

        $doctrineConnection = $databaseConnection->getDoctrineConnection();

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

        $schemaManager = $doctrineConnection->getSchemaManager();

        $relativePath = $this->createReadmePath($connection);

        $this->events->dispatch(new BuildingReadme($relativePath));

        yield $relativePath => $this->view->make('schemarkdown::schema', [
            'schema' => new Schema($schemaManager, $databaseConnection->getDatabaseName()),
        ])->render();

        foreach ($schemaManager->listTableNames() as $tableName) {
            $relativePath = $this->createRelativePath($connection, $tableName);

            $this->events->dispatch(new BuildingSchema($relativePath));

            yield $relativePath => $this->view->make('schemarkdown::table', [
                'table' => new Table(
                    $schemaManager->listTableDetails($tableName),
                    $databaseConnection->getDatabaseName()
                ),
            ])->render();
        }
    }

    /**
     * @param string $connection
     * @param string $table
     * @return string
     */
    private function createRelativePath($connection, $table): string
    {
        if ($this->withConnectionNamespace) {
            return Str::snake($connection) . '/' . Str::snake($table) . '.md';
        }

        return Str::snake($table) . '.md';
    }

    /**
     * @param string $connection
     * @return string
     */
    private function createReadmePath($connection): string
    {
        if ($this->withConnectionNamespace) {
            return Str::snake($connection) . '/README.md';
        }

        return 'README.md';
    }
}
