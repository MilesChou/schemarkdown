<?php

namespace MilesChou\Schemarkdown\Console;

use Illuminate\Console\Command;
use MilesChou\Codegener\Traits\Path;
use MilesChou\Codegener\Writer;
use MilesChou\Schemarkdown\Schema\ModelGenerator;

class SchemaModelCommand extends Command
{
    use Concerns\InitializeConnection;
    use Path;

    /**
     * @var string
     */
    protected $description = 'Generate eloquent model classes from database schema';

    /**
     * @var string
     */
    protected $signature = 'schema:model
                                {--memory-limit=-1 : MEMORY_LIMIT config}
                                {--config-file=config/database.php : Config file}
                                {--connection= : Connection name will only build}
                                {--output-dir=app/Models : Relative path with getcwd()}
                                {--namespace=App/Models : Namespace prefix}
                                {--overwrite : Overwrite the exist file}
                                ';

    public function handle(ModelGenerator $builder, Writer $writer)
    {
        $memoryLimit = $this->option('memory-limit');
        $configFile = $this->option('config-file');
        $connection = $this->option('connection');
        $outputDir = $this->option('output-dir');
        $namespace = $this->option('namespace');
        $overwrite = $this->option('overwrite');

        ini_set('memory_limit', $memoryLimit);

        $this->initializeConnection($this->laravel, $configFile, $connection);

        $buildCode = $builder->setNamespace($namespace)->build();

        $writer->appendBasePath($outputDir)
            ->writeMass($buildCode, $overwrite);
    }
}
