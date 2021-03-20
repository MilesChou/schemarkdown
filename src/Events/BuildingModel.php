<?php

namespace MilesChou\Schemarkdown\Events;

class BuildingModel
{
    public $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }
}
