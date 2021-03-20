<?php

declare(strict_types=1);

namespace MilesChou\Schemarkdown\Models;

use BadMethodCallException;
use Doctrine\DBAL\Schema\Table as DoctrineTable;
use Illuminate\Support\Collection;

/**
 * @mixin DoctrineTable
 */
class Table
{
    /**
     * @var string
     */
    private $database;

    /**
     * @var DoctrineTable
     */
    private $DoctrineTable;

    /**
     * @param DoctrineTable $doctrineTable
     * @param string $database
     */
    public function __construct(DoctrineTable $doctrineTable, string $database)
    {
        $this->DoctrineTable = $doctrineTable;
        $this->database = $database;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->DoctrineTable, $name)) {
            return $this->DoctrineTable->$name(...$arguments);
        }

        throw new BadMethodCallException('Undefined method ' . $name . ' in class ' . static::class);
    }

    public function comment(): string
    {
        // Workaround for PHP 7.1
        return $this->DoctrineTable->hasOption('comment') ? $this->DoctrineTable->getOption('comment') : '';
    }

    /**
     * @return Collection
     */
    public function columns(): Collection
    {
        return collect($this->DoctrineTable->getColumns())->transform(function ($value) {
            return new Column($value);
        });
    }

    /**
     * Database name
     *
     * @return string
     */
    public function database(): string
    {
        return $this->database;
    }

    /**
     * @return Collection
     */
    public function indexes(): Collection
    {
        return collect($this->DoctrineTable->getIndexes())->transform(function ($value) {
            return new Index($value);
        });
    }
}
