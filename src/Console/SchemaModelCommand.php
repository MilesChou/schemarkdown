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
    protected $description = 'Generate Eloquent Model classes from the given database schema';

    /**
     * @var string
     */
    protected $signature = 'schema:model
                                {--database= : Connection name will only build}
                                {--config=config/database.php : Config file}
                                {--path=app/Models : The path where the model file should be stored}
                                {--namespace=App\\Models : Namespace prefix}
                                {--overwrite : Overwrite the exist file}
                                {--memory-limit=-1 : memory limit config in php.ini}
                                ';

    public function handle(ModelGenerator $generator, Writer $writer): int
    {
        $database = $this->option('database');
        $config = $this->option('config');
        $path = $this->option('path');
        $namespace = $this->option('namespace');

        ini_set('memory_limit', $this->option('memory-limit'));

        $this->initializeConnection($this->laravel, $config, $database);

        $buildCode = $generator->setNamespace($namespace)->build();

        if ($this->output->isVerbose()) {
            $this->comment('Building successfully');
        }

        $writer->appendBasePath($path)
            ->writeMass($buildCode, $this->option('overwrite'));

        $this->info('Generate model successfully');

        return 0;
    }
}
