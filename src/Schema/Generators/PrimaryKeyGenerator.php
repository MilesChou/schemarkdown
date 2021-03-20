<?php

namespace MilesChou\Schemarkdown\Schema\Generators;

use Illuminate\Support\Collection;
use MilesChou\Schemarkdown\Models\Column;

class PrimaryKeyGenerator
{
    /**
     * @param Collection $indexes
     * @param array $fields
     * @return string
     */
    public function generate(Collection $indexes, array $fields): string
    {
        $fieldsWithPk = array_filter($fields, static function (Column $attr) use ($indexes) {
            $indexAttr = $indexes->get($attr->getName());

            return isset($indexAttr->type) && 'primary' === $indexAttr->type;
        });

        $pks = array_values(array_map(
            static function ($attr) {
                return $attr['field'];
            },
            $fieldsWithPk
        ));

        return $this->buildArrayCode($pks);
    }

    protected function buildArrayCode(array $pks): string
    {
        if (count($pks) !== 1) {
            return 'null';
        }

        return "'{$pks[0]}'";
    }
}
