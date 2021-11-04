<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\PhpReflection;

final class PhpReflectionTest extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function testInitialize(): void {
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename);

        $this->assertNotNull($class, 'initialize PhppReflection');
    }

    public function testDump(): void {
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename);

        $data = $class->getInfo();
        $this->assertSame($data->name, 'Product', 'class name.');
        $this->assertSame($data->namespace, '', 'namespace name.');
        $this->assertSame($data->properties[0]->name, 'name', 'type.');
        $this->assertSame($data->properties[0]->type, 'Name', 'type.');
        $this->assertSame($data->properties[1]->name, 'price', 'name.');
        $this->assertSame($data->properties[1]->type, 'Price', 'type.');
    }
}
