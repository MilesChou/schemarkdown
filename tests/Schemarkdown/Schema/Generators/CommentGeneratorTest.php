<?php

namespace Tests\Schemarkdown\Schema\Generators;

use LaravelBridge\Scratch\Application as LaravelBridge;
use MilesChou\Schemarkdown\Schema\Generators\CommentGenerator;
use Tests\TestCase;

/**
 * @covers \MilesChou\Schemarkdown\Schema\Generators\CommentGenerator
 */
class CommentGeneratorTest extends TestCase
{
    /**
     * @var CommentGenerator
     */
    private $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = LaravelBridge::getInstance()->make(CommentGenerator::class);
    }

    protected function tearDown(): void
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectContent(): void
    {
        $property = $this->createColumns([
            'field_a' => 'integer',
            'field_b' => 'text',
            'field_c' => 'decimal',
        ]);

        $actual = $this->target->generate($property);

        $this->assertStringContainsString('int field_a', $actual);
        $this->assertStringContainsString('string field_b', $actual);
        $this->assertStringContainsString('float field_c', $actual);

        // Should remove tail space
        $this->assertDoesNotMatchRegularExpression('/\\s+\n/', $actual);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenNoField(): void
    {
        $this->assertSame('', $this->target->generate([]));
    }
}
