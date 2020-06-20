<?php

declare(strict_types=1);

namespace MilesChou\Schemarkdown\Events;

class BuildingConnection
{
    public $connection;

    public function __construct(string $connection)
    {
        $this->connection = $connection;
    }
}
