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
        $this->assertSame('Product', $data->type->name, 'class type name.');
        $this->assertSame([], $data->type->namespace, 'namespace name.');
        $this->assertSame('name', $data->properties[0]->name, 'type.');
        $this->assertSame('Name', $data->properties[0]->type->name, 'type.');
        $this->assertSame([], $data->properties[0]->type->namespace, 'namespace.');
        $this->assertSame('price', $data->properties[1]->name, 'name.');
        $this->assertSame('Price', $data->properties[1]->type->name, 'type.');
        $this->assertSame([], $data->properties[1]->type->namespace, 'namespace.');
    }

    public function testDump_with_namespace(): void {
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->type->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->type->namespace, 'namespace name.');
        $this->assertSame('name', $data->properties[0]->name, 'type.');
        $this->assertSame('Name', $data->properties[0]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->properties[0]->type->namespace, 'namespace.');
        $this->assertSame('price', $data->properties[1]->name, 'name.');
        $this->assertSame('Price', $data->properties[1]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->properties[1]->type->namespace, 'namespace.');
    }

    public function testDump_with_phpdoc(): void {
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->type->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->type->namespace, 'namespace name.');
        $this->assertSame('name', $data->properties[0]->name, 'type.');
        $this->assertSame('Name', $data->properties[0]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->properties[0]->type->namespace, 'Name namespace.');
        $this->assertSame('price', $data->properties[1]->name, 'name.');
        $this->assertSame('Price', $data->properties[1]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->properties[1]->type->namespace, 'Price namespace.');
        $this->assertSame('Tag[]', $data->properties[2]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->properties[2]->type->namespace, 'Tag[] namespace.');
    }
}
