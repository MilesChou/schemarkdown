<?php

namespace Tests;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use MilesChou\Schemarkdown\Models\Column;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createColumn(string $field, string $type, array $options = []): Column
    {
        return new Column(new DoctrineColumn($field, Type::getType($type), $options));
    }

    protected function createColumns(array $fields): array
    {
        return array_map(function ($k, $v) {
            return $this->createColumn($k, $v);
        }, array_keys($fields), $fields);
    }
}
