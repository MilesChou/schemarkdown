<?php

namespace Tests\Schemarkdown\Schema\Generators;

use MilesChou\Schemarkdown\Models\Column;
use MilesChou\Schemarkdown\Schema\Generators\PropertyGenerator;
use Tests\TestCase;

/**
 * @covers \MilesChou\Schemarkdown\Schema\Generators\PropertyGenerator
 */
class PropertyGeneratorTest extends TestCase
{
    /**
     * @var PropertyGenerator
     */
    private $target;

    public function defaultFields(): array
    {
        return [
            ['int', $this->createColumn('whatever', 'smallint')],
            ['int', $this->createColumn('whatever', 'integer')],
            ['int', $this->createColumn('whatever', 'bigint')],
            ['float', $this->createColumn('whatever', 'decimal')],
            ['float', $this->createColumn('whatever', 'float')],
            ['string', $this->createColumn('whatever', 'string')],
            ['string', $this->createColumn('whatever', 'text')],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new PropertyGenerator();
    }

    protected function tearDown(): void
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider defaultFields
     * @param string $exceptedType
     * @param Column $property
     */
    public function shouldReturnCorrectTypeWithDefaultFields($exceptedType, $property): void
    {
        $excepted = "{$exceptedType} {$property->getName()}";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnNullWordWhenColumnIsNullable(): void
    {
        $property = $this->createColumn('whatever', 'integer', [
            'notnull' => false,
        ]);

        $excepted = "null|int whatever";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnCommentWordWhenColumnHasComment(): void
    {
        $property = $this->createColumn('whatever', 'integer', [
            'comment' => 'some-comment',
        ]);

        $excepted = "int whatever some-comment";

        $this->assertSame($excepted, $this->target->generate($property));
    }

    /**
     * @test
     */
    public function shouldReturnCommentWordWithoutTailSpaceWhenColumnHasCommentWithTailSpace(): void
    {
        $property = $this->createColumn('whatever', 'integer', [
            'comment' => 'some-comment     ',
        ]);

        $excepted = "int whatever some-comment";

        $this->assertSame($excepted, $this->target->generate($property));
    }
}
