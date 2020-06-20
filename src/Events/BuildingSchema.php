<?php

declare(strict_types=1);

namespace MilesChou\Schemarkdown\Events;

class BuildingSchema
{
    public $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }
}
