<?php

namespace Tests;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type;
use Illuminate\Console\Application;
use Illuminate\Container\Container;
use MilesChou\Schemarkdown\Models\Column;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup('test');

        $this->putConfigFileWithVfs();

        $this->container = $this->createContainer();
    }

    protected function tearDown(): void
    {
        $this->container = null;

        parent::tearDown();
    }

    protected function createContainer()
    {
        /** @var Application $app */
        $app = require __DIR__ . '/../bootstrap/app.php';

        return $app->getLaravel();
    }

    /**
     * @param string $path
     * @param array $config
     */
    protected function putConfigFileWithVfs(array $config = [], $path = '/config/database.php'): void
    {
        if (!array_key_exists('connections', $config)) {
            $config = ['connections' => $config];
        }

        $filename = $this->root->url() . $path;

        mkdir(dirname($filename), 0777, true);

        $code = '<?php return ' . var_export($config, true) . ';';

        file_put_contents($filename, $code);
    }

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
