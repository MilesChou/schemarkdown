<?php

namespace MilesChou\Schemarkdown\Console;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use MilesChou\Codegener\Traits\Path;
use MilesChou\Codegener\Writer;
use MilesChou\Schemarkdown\Schema\MarkdownGenerator;

class SchemaMarkdownCommand extends Command
{
    use Concerns\ConnectionConfig;
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
                                {--output-dir=generated/markdown : Relative path with getcwd()}
                                {--overwrite : Overwrite the exist file}';

    public function handle(MarkdownGenerator $generator): int
    {
        $memoryLimit = $this->option('memory-limit');
        $outputDir = $this->option('output-dir');
        $overwrite = $this->option('overwrite');

        ini_set('memory_limit', $memoryLimit);

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
