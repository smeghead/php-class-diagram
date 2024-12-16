<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\ClassIdentifier;
use Smeghead\PhpClassDiagram\DiagramElement\Entry;
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class ClassIdentifierTest extends TestCase
{
    private string $fixtureDir;

    public function setUp(): void
    {
        $this->fixtureDir = realpath(sprintf('%s/../fixtures', __DIR__));
    }

    public function testClass(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);

        $options = new Options([]);
        $entry = $this->getTargetEntry('product/Product.php', $directory, $options);

        $sut = new ClassIdentifier($options, 'product', $entry);

        $this->assertSame('class "Product" as product_Product', $sut->getIdentifier());
    }

    public function testInterface(): void
    {
        $directory = sprintf('%s/interface', $this->fixtureDir);

        $options = new Options([]);
        $entry = $this->getTargetEntry('product/Interface_.php', $directory, $options);

        $sut = new ClassIdentifier($options, 'product', $entry);

        $this->assertSame('interface "Interface_" as product_Interface_', $sut->getIdentifier());
    }

    public function testEnum(): void
    {
        $directory = sprintf('%s/enum', $this->fixtureDir);

        $options = new Options([]);
        $entry = $this->getTargetEntry('TestEnum.php', $directory, $options);

        $sut = new ClassIdentifier($options, '', $entry);

        $this->assertSame('enum "Suit\n<b>スート</b>" as Suit', $sut->getIdentifier());
    }

    public function testClassRelTarget(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);

        $options = new Options(['rel-target' => 'Product,Name']);
        $entry = $this->getTargetEntry('product/Product.php', $directory, $options);

        $sut = new ClassIdentifier($options, 'product', $entry);

        $this->assertSame('class "Product" as product_Product #FFF0F5;line:740125;text:740125', $sut->getIdentifier());
    }

    public function testClassRelTargetNotMatch(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);

        $options = new Options(['rel-target' => 'ProductXX,Name']);
        $entry = $this->getTargetEntry('product/Product.php', $directory, $options);

        $sut = new ClassIdentifier($options, 'product', $entry);

        $this->assertSame('class "Product" as product_Product', $sut->getIdentifier());
    }

    private function getTargetEntry(string $path, string $directory, Options $options): Entry
    {
        $filename = sprintf('%s/%s', $directory, $path);
        $classes = PhpReader::parseFile($directory, $filename, $options);
        if (count($classes) === 0) {
            throw new \Exception('class not found.');
        }
        return new Entry(dirname($path), $classes[0]->getInfo(), $options); 
    }

}
