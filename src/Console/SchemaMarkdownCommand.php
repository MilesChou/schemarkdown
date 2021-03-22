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
    protected $description = 'Generate Markdown document from database schema';

    /**
     * @var string
     */
    protected $signature = 'schema:markdown
                                {--memory-limit=-1 : MEMORY_LIMIT config}
                                {--config-file=config/database.php : Config file}
                                {--connection= : Connection name will only build}
                                {--output-dir=docs/schema : Relative path with getcwd()}
                                {--overwrite : Overwrite the exist file}
                                ';

    public function handle(MarkdownGenerator $generator): int
    {
        $memoryLimit = $this->option('memory-limit');
        $configFile = $this->option('config-file');
        $connection = $this->option('connection');
        $outputDir = $this->option('output-dir');
        $overwrite = $this->option('overwrite');

        ini_set('memory_limit', $memoryLimit);

        $this->initializeConnection($this->laravel, $configFile, $connection);

        $code = $generator->build();

        $this->output->success('All document build success, next will write files');

        /** @var Writer $writer */
        $writer = $this->laravel->make(Writer::class);
        $writer->appendBasePath($outputDir)
            ->writeMass($code, $overwrite);

        $this->output->success('All document write success');

        return 0;
    }
}
