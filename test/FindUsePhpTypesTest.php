<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class FindUsePhpTypesTest extends TestCase
{
    private string $fixtureDir;
    public function setUp(): void
    {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function testMainUseProduct(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $filename = sprintf('%s/%s', $directory, 'product/Main.php');
        $options = new Options([]);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $class = $classes[0]->getInfo();
        $types = $class->getUsingTypes();
        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace());
        $this->assertSame('Product', $types[0]->getName());
    }
}
