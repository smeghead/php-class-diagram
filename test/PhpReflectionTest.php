<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\ {
    Options,
    PhpReflection,
};

final class PhpReflectionTest extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function testInitialize(): void {
        $options = new Options([]);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

        $this->assertNotNull($class, 'initialize PhppReflection');
    }

    public function testDump(): void {
        $options = new Options([]);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

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
        $options = new Options([]);
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

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
        $options = new Options([]);
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->type->name, 'class type name.');
        $this->assertSame('Stmt_Class', $data->type->meta, 'class meta name.');
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

    public function testDump_with_interface(): void {
        $options = new Options([]);
        $filename = sprintf('%s/interface/product/Interface_.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Interface_', $data->type->name, 'class type name.');
        $this->assertSame('Stmt_Interface', $data->type->meta, 'class meta name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->type->namespace, 'namespace name.');
        $this->assertSame('name', $data->properties[0]->name, 'property type.');
        $this->assertSame('string', $data->properties[0]->type->name, 'property type.');
    }
    public function testDump_with_methods(): void {
        $options = new Options([]);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->type->name, 'class type name.');
        $this->assertSame([], $data->type->namespace, 'namespace name.');
        $this->assertSame('method1', $data->methods[0]->name, 'namespace name.');
        $this->assertSame('param1', $data->methods[0]->params[0]->name, 'parameter name.');
    }
    public function testDump_with_methods2(): void {
        $options = new Options([]);
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->type->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->type->namespace, 'namespace name.');
        $this->assertSame('method1', $data->methods[0]->name, 'namespace name.');
        $this->assertSame('param1', $data->methods[0]->params[0]->name, 'parameter name.');
    }
    public function testDump_with_extend(): void {
        $options = new Options([]);
        $filename = sprintf('%s/extends/product/Sub.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Sub', $data->type->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->type->namespace, 'namespace name.');
        $this->assertSame('Super', $data->extends[0]->name, 'super class name.');
    }
    public function testDump_with_implements(): void {
        $options = new Options([]);
        $filename = sprintf('%s/extends/product/Implements_.php', $this->fixtureDir);
        $class = new PhpReflection($filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Implements_', $data->type->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->type->namespace, 'namespace name.');
        $this->assertSame('Interface_', $data->extends[0]->name, 'super class name.');
    }
}
