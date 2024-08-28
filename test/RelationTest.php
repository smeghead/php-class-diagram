<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\{
    Entry,
    Relation,
};
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class RelationTest extends TestCase
{
    private string $fixtureDir;
    public function setUp(): void
    {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function tearDown(): void
    {
        $this->fixtureDir = '';

        parent::tearDown();
    }

    public function testInitialize(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];
        $rel = $this->getRelation($files, $directory, $options);

        $this->assertNotNull($rel, 'initialize Relation');
    }

    public function testGetRelations1(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);
        $relations = $rel->getRelations();

        $this->assertCount(3, $relations, 'count');
        $this->assertSame('  product_Product ..> product_Name', $relations[0], 'relation 1');
        $this->assertSame('  product_Product ..> product_Price', $relations[1], 'relation 2');
        $this->assertSame('  product_Product ..> product_Product', $relations[2], 'relation 3');
    }
    public function testGetRelations_in_code_dependency(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Main.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);
        $relations = $rel->getRelations();

        $this->assertCount(2, $relations, 'count');
        $this->assertSame('  product_Main ..> product_Product', $relations[0], 'relation 0');
    }

    public function testGetRelations2(): void
    {
        $directory = sprintf('%s/namespace-tag', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
            'product/Tag.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);
        $relations = $rel->getRelations();

        $this->assertCount(3, $relations, 'count');
        $this->assertSame('  product_Product "1" ..> "*" product_Tag', $relations[0], 'relation 1');
        $this->assertSame('  product_Product ..> product_Name', $relations[1], 'relation 2');
        $this->assertSame('  product_Product ..> product_Price', $relations[2], 'relation 3');
    }

    public function testGetRelations_array_expression_in_doc(): void
    {
        $directory = sprintf('%s/array-expression-in-doc', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Tag.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);
        $relations = $rel->getRelations();

        $this->assertCount(2, $relations, 'count');
        $this->assertSame('  product_Product "1" ..> "*" product_Tag', $relations[0], 'relation *');
        $this->assertSame('  product_Product "1" ..> "1..*" product_Tag', $relations[1], 'relation 1..*');
    }

    public function testGetRelations_extends1(): void
    {
        $directory = sprintf('%s/namespace-tag', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Tag.php',
            'product/SubTag.php',
        ];
        $rel = $this->getRelation($files, $directory, $options);
        $relations = $rel->getRelations();

        $this->assertCount(1, $relations, 'count');
        $this->assertSame('  product_Tag <|-- product_SubTag', $relations[0], 'relation 1');
    }

    /**
     * @param string[] $files
     */
    private function getRelation(array $files, string $directory, Options $options): Relation
    {
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            $entries[] = array_map(fn($c) => new Entry(dirname($f), $c->getInfo(), $options), $classes);
        }

        return new Relation(array_merge(...$entries), $options);
    }
}
