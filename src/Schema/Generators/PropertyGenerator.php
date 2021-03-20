<?php

namespace MilesChou\Schemarkdown\Schema\Generators;

use MilesChou\Schemarkdown\Models\Column;

class PropertyGenerator
{
    /**
     * @var array
     */
    protected $mapping = [
        // boolean fields
        'boolean' => 'bool',

        // int fields
        'smallint' => 'int',
        'smallInteger' => 'int',
        'integer' => 'int',
        'bigint' => 'int',
        'bigInteger' => 'int',

        // float fields
        'decimal' => 'float',
        'float' => 'float',

        // string fields
        'char' => 'string',
        'string' => 'string',
        'text' => 'string',

        // date and time fields
        'dateTime' => '\\Carbon\\Carbon',
        'timestamps' => '\\Carbon\\Carbon',
    ];

    /**
     * @param Column $column
     * @return string
     */
    public function generate(Column $column): string
    {
        $type = $this->mapping[$column->getType()->getName()] ?? 'mixed';
        $field = $column->getName();

        $modelProperty = "{$type} {$field}";
        $modelProperty = $this->resolveDecorators($column, $modelProperty);

        // Should remove tail space
        return trim($modelProperty);
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    /**
     * @param string $type
     * @param string $propertyType
     */
    public function setMapping(string $type, string $propertyType): void
    {
        $this->mapping[$type] = $propertyType;
    }

    /**
     * @param Column $column
     * @param string $modelProperty
     * @return string
     */
    private function resolveDecorators(Column $column, string $modelProperty): string
    {
        if (!$column->getNotnull()) {
            $modelProperty = 'null|' . $modelProperty;
        }

        if ($comment = $column->getComment()) {
            $modelProperty .= ' ' . trim($comment);
        }

        return $modelProperty;
    }
}
