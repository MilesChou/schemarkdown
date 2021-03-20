<?php

namespace MilesChou\Schemarkdown\Schema\Generators;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use MilesChou\Schemarkdown\Models\Table;

class CodeGenerator
{
    /**
     * @var CommentGenerator
     */
    private $commentGenerator;

    /**
     * @var PrimaryKeyGenerator
     */
    private $primaryKeyGenerator;

    /**
     * @var ViewFactory
     */
    private $view;

    /**
     * @param CommentGenerator $commentGenerator
     * @param PrimaryKeyGenerator $primaryKeyGenerator
     * @param ViewFactory $view
     */
    public function __construct(
        CommentGenerator $commentGenerator,
        PrimaryKeyGenerator $primaryKeyGenerator,
        ViewFactory $view
    ) {
        $this->commentGenerator = $commentGenerator;
        $this->primaryKeyGenerator = $primaryKeyGenerator;
        $this->view = $view;
    }

    /**
     * @param Table $table
     * @param string $namespace
     * @param $connection
     * @param false $withConnectionNamespace
     * @return string
     */
    public function generate(
        Table $table,
        string $namespace,
        Connection $connection,
        $withConnectionNamespace = false
    ): string {
        if ($withConnectionNamespace) {
            $namespace .= '\\' . ucfirst($connection->getName());
        }

        $fields = collect($table->columns())->filter(function ($value, $key) {
            return is_string($key);
        })->toArray();

        return $this->view->make('schemarkdown::model', [
            'comment' => $this->commentGenerator->generate($fields),
            'connection' => $connection->getName(),
            'name' => Str::studly($table->getName()),
            'namespace' => $namespace,
            'pk' => $this->primaryKeyGenerator->generate($table->indexes(), $fields),
            'table' => $table->getName(),
        ])->render();
    }
}
