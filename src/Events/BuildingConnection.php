<?php

declare(strict_types=1);

namespace MilesChou\Schemarkdown\Events;

use Illuminate\Database\Connection;

class BuildingConnection
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Connection
     */
    public $connection;

    public function __construct(string $name, Connection $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
    }
}
