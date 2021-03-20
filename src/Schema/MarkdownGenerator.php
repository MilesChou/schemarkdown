<?php

declare(strict_types=1);

namespace MilesChou\Schemarkdown\Schema;

use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use MilesChou\Schemarkdown\Events\BuildingConnection;
use MilesChou\Schemarkdown\Events\BuildingReadme;
use MilesChou\Schemarkdown\Events\BuildingSchema;
use MilesChou\Schemarkdown\Models\Schema;
use MilesChou\Schemarkdown\Models\Table;
use MilesChou\Schemarkdown\Schema\Concerns\DatabaseConnection;

class MarkdownGenerator
{
    use DatabaseConnection;

    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var Events
     */
    private $events;

    /**
     * @var View
     */
    private $view;

    /**
     * @param DatabaseManager $databaseManager The manager has been connected
     * @param View $view
     * @param Events $events
     */
    public function __construct(DatabaseManager $databaseManager, View $view, Events $events)
    {
        $this->databaseManager = $databaseManager;
        $this->view = $view;
        $this->events = $events;
    }

    /**
     * @return iterable [filepath => code]
     * @throws \Doctrine\DBAL\Exception
     */
    public function build(): iterable
    {
        $connections = $this->databaseManager->getConnections();
        $withConnectionNamespace = count($connections) > 1;

        foreach ($connections as $name => $connection) {
            $this->events->dispatch(new BuildingConnection($name, $connection));

            yield from $this->buildDocs($name, $connection, $withConnectionNamespace);
        }
    }

    /**
     * @param string $name
     * @param Connection $connection
     * @param bool $withConnectionNamespace
     * @return iterable
     * @throws \Doctrine\DBAL\Exception
     */
    private function buildDocs(string $name, Connection $connection, bool $withConnectionNamespace): iterable
    {
        $schemaManager = $this->resolveSchemaManger($connection);

        $filename = $this->createReadmePath($name, $withConnectionNamespace);

        $this->events->dispatch(new BuildingReadme($filename));

        yield $filename => $this->view->make('schemarkdown::schema', [
            'schema' => new Schema($schemaManager, $connection->getDatabaseName()),
        ])->render();

        foreach ($schemaManager->listTableNames() as $tableName) {
            $filename = $this->createRelativePath($name, $tableName, $withConnectionNamespace);

            $this->events->dispatch(new BuildingSchema($filename));

            yield $filename => $this->view->make('schemarkdown::table', [
                'table' => new Table(
                    $schemaManager->listTableDetails($tableName),
                    $connection->getDatabaseName()
                ),
            ])->render();
        }
    }

    /**
     * @param string $connection
     * @param string $table
     * @param bool $withConnectionNamespace
     * @return string
     */
    private function createRelativePath(string $connection, string $table, bool $withConnectionNamespace): string
    {
        if ($withConnectionNamespace) {
            return Str::snake($connection) . '/' . Str::snake($table) . '.md';
        }

        return Str::snake($table) . '.md';
    }

    /**
     * @param string $connection
     * @param bool $withConnectionNamespace
     * @return string
     */
    private function createReadmePath(string $connection, bool $withConnectionNamespace): string
    {
        if ($withConnectionNamespace) {
            return Str::snake($connection) . '/README.md';
        }

        return 'README.md';
    }
}
