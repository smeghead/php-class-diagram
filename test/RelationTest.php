<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\ {
    Entry,
    Relation,
};
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class RelationTest extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function testInitialize(): void {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            foreach ($classes as $c) {
                $entries = array_merge($entries, [new Entry(dirname($f), $c->getInfo(), $options)]);
            }
        }
        $rel = new Relation($entries, $options);

        $this->assertNotNull($rel, 'initialize Relation');
    }

    public function testGetRelations1(): void {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            foreach ($classes as $c) {
                $entries = array_merge($entries, [new Entry(dirname($f), $c->getInfo(), $options)]);
            }
        }
        $rel = new Relation($entries, $options);
        $relations = $rel->getRelations();

        $this->assertSame(3, count($relations), 'count');
        $this->assertSame('  product_Product ..> product_Name', $relations[0], 'relation 1');
        $this->assertSame('  product_Product ..> product_Price', $relations[1], 'relation 2');
        $this->assertSame('  product_Product ..> product_Product', $relations[2], 'relation 3');
    }

    public function testGetRelations2(): void {
        $directory = sprintf('%s/namespace-tag', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
            'product/Tag.php',
        ];
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            foreach ($classes as $c) {
                $entries = array_merge($entries, [new Entry(dirname($f), $c->getInfo(), $options)]);
            }
        }
        $rel = new Relation($entries, $options);
        $relations = $rel->getRelations();

        $this->assertSame(3, count($relations), 'count');
        $this->assertSame('  product_Product "1" ..> "*" product_Tag', $relations[0], 'relation 1');
        $this->assertSame('  product_Product ..> product_Name', $relations[1], 'relation 2');
        $this->assertSame('  product_Product ..> product_Price', $relations[2], 'relation 3');
    }

    public function testGetRelations_extends1(): void {
        $directory = sprintf('%s/namespace-tag', $this->fixtureDir);
        $options = new Options([]);
        $files = [
            'product/Tag.php',
            'product/SubTag.php',
        ];
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            foreach ($classes as $c) {
                $entries = array_merge($entries, [new Entry(dirname($f), $c->getInfo(), $options)]);
            }
        }
        $rel = new Relation($entries, $options);
        $relations = $rel->getRelations();

        $this->assertSame(1, count($relations), 'count');
        $this->assertSame('  product_Tag <|-- product_SubTag', $relations[0], 'relation 1');
    }
}
