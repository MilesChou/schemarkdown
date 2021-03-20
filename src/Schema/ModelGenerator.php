<?php

namespace MilesChou\Schemarkdown\Schema;

use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use MilesChou\Schemarkdown\Events\BuildingConnection;
use MilesChou\Schemarkdown\Events\BuildingModel;
use MilesChou\Schemarkdown\Models\Table;
use MilesChou\Schemarkdown\Schema\Concerns\DatabaseConnection;
use MilesChou\Schemarkdown\Schema\Generators\CodeGenerator;

class ModelGenerator
{
    use DatabaseConnection;

    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    /**
     * @var array
     */
    private $connections;

    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var SchemaGenerator[]
     */
    private $schemaGenerators;

    /**
     * @var Events
     */
    private $events;

    /**
     * @param DatabaseManager $databaseManager
     * @param CodeGenerator $codeGenerator
     * @param Events $events
     */
    public function __construct(DatabaseManager $databaseManager, CodeGenerator $codeGenerator, Events $events)
    {
        $this->databaseManager = $databaseManager;
        $this->codeGenerator = $codeGenerator;
        $this->events = $events;
    }

    /**
     * @return iterable [filepath => code]
     */
    public function build(): iterable
    {
        $connections = $this->databaseManager->getConnections();
        $withConnectionNamespace = count($connections) > 1;

        foreach ($connections as $name => $connection) {
            $this->events->dispatch(new BuildingConnection($name, $connection));

            yield from $this->buildCode($name, $connection, $withConnectionNamespace);
        }
    }

    /**
     * @param string $namespace
     * @return static
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param string $name
     * @param Connection $connection
     * @param bool $withConnectionNamespace
     * @return iterable
     * @throws \Doctrine\DBAL\Exception
     */
    private function buildCode(string $name, Connection $connection, bool $withConnectionNamespace): iterable
    {
        $schemaManager = $this->resolveSchemaManger($connection);

        foreach ($schemaManager->listTables() as $table) {
            $filename = $this->createRelativePath($name, $table->getName(), $withConnectionNamespace);

            $this->events->dispatch(new BuildingModel($filename));

            $code = $this->codeGenerator->generate(
                new Table($table, $name),
                $this->namespace,
                $connection,
                $withConnectionNamespace
            );

            yield $filename => $code;
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
            return Str::studly($connection) . '/' . Str::studly($table) . '.php';
        }

        return Str::studly($table) . '.php';
    }
}
