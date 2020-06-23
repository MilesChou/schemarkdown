<?php

namespace MilesChou\Schemarkdown\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\DatabaseManager;
use MilesChou\Codegener\Traits\Path;
use MilesChou\Codegener\Writer;
use MilesChou\Schemarkdown\Builder;

class SchemarkdownCommand extends Command
{
    use Concerns\DatabaseConnection;
    use Path;

    /**
     * @var string
     */
    protected $name = 'schemarkdown';

    /**
     * @var string
     */
    protected $signature = 'schemarkdown
                                {--memory-limit=-1 : MEMORY_LIMIT config}
                                {--config-file=config/database.php : Config file}
                                {--connection= : Connection name will only build}
                                {--output-dir=generated : Relative path with getcwd()}
                                {--overwrite : Overwrite the exist file}';

    public function handle()
    {
        $memoryLimit = $this->input->getOption('memory-limit');
        $configFile = $this->input->getOption('config-file');
        $connection = $this->input->getOption('connection');
        $outputDir = $this->input->getOption('output-dir');
        $overwrite = $this->input->getOption('overwrite');

        ini_set('memory_limit', $memoryLimit);

        $this->laravel['config']['database.connections'] = $this->normalizeConnectionConfig(
            $this->formatPath($configFile)
        );

        if ($connection === null) {
            $connections = array_keys($this->laravel['config']['database.connections']);
        } else {
            $connections = [$connection];
        }

        // Initial connection
        /** @var DatabaseManager $databaseManager */
        $databaseManager = $this->laravel->get('db');

        foreach ($connections as $connection) {
            $databaseManager->connection($connection);
        }

        $code = (new Builder(
            $databaseManager,
            $this->laravel->make('view'),
            $this->laravel->make('events')
        ))->build();

        $this->output->success('All document build success, next will write files');

        /** @var Writer $writer */
        $writer = $this->laravel->make(Writer::class);
        $writer->appendBasePath($outputDir)
            ->writeMass($code, $overwrite);

        $this->output->success('All document write success');

        return 0;
    }
}
