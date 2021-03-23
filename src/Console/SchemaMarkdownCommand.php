<?php

namespace MilesChou\Schemarkdown\Console;

use Illuminate\Console\Command;
use MilesChou\Codegener\Traits\Path;
use MilesChou\Codegener\Writer;
use MilesChou\Schemarkdown\Schema\MarkdownGenerator;

class SchemaMarkdownCommand extends Command
{
    use Concerns\InitializeConnection;
    use Path;

    /**
     * @var string
     */
    protected $description = 'Generate Markdown document from the given database connection';

    /**
     * @var string
     */
    protected $signature = 'schema:markdown
                                {--database= : The database connection to use}
                                {--config=config/database.php : Config file path}
                                {--path=docs/schema : The path where the .md file should be stored}
                                {--overwrite : Overwrite the exist file}
                                {--memory-limit=-1 : memory limit config in php.ini}
                                ';

    public function handle(MarkdownGenerator $generator, Writer $writer): int
    {
        $database = $this->option('database');
        $config = $this->option('config');
        $path = $this->option('path');

        ini_set('memory_limit', $this->option('memory-limit'));

        $this->initializeConnection($this->laravel, $config, $database);

        $code = $generator->build();

        if ($this->output->isVerbose()) {
            $this->comment('Building successfully');
        }

        $writer->appendBasePath($path)
            ->writeMass($code, $this->option('overwrite'));

        $this->info('Generate document successfully');

        return 0;
    }
}
