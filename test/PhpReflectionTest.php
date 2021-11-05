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
        $this->assertSame($data->type->name, 'Product', 'class type name.');
        $this->assertSame($data->type->namespace, [], 'namespace name.');
        $this->assertSame($data->properties[0]->name, 'name', 'type.');
        $this->assertSame($data->properties[0]->type->name, 'Name', 'type.');
        $this->assertSame($data->properties[0]->type->namespace, [], 'namespace.');
        $this->assertSame($data->properties[1]->name, 'price', 'name.');
        $this->assertSame($data->properties[1]->type->name, 'Price', 'type.');
        $this->assertSame($data->properties[1]->type->namespace, [], 'namespace.');
    }

    public function testDump_with_namespace(): void {
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename);

        $data = $class->getInfo();
        $this->assertSame($data->type->name, 'Product', 'class type name.');
        $this->assertSame($data->type->namespace, ['hoge', 'fuga', 'product'], 'namespace name.');
        $this->assertSame($data->properties[0]->name, 'name', 'type.');
        $this->assertSame($data->properties[0]->type->name, 'Name', 'type.');
        $this->assertSame($data->properties[0]->type->namespace, ['hoge', 'fuga', 'product'], 'namespace.');
        $this->assertSame($data->properties[1]->name, 'price', 'name.');
        $this->assertSame($data->properties[1]->type->name, 'Price', 'type.');
        $this->assertSame($data->properties[1]->type->namespace, ['hoge', 'fuga', 'product'], 'namespace.');
    }
}
