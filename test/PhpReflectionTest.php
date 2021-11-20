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
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $this->assertNotNull($class, 'initialize PhppReflection');
    }

    public function testDump(): void {
        $options = new Options([]);
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->getClassType()->name, 'class type name.');
        $this->assertSame([], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->name, 'property name.');
        $this->assertSame('Name', $data->getProperties()[0]->type->name, 'property name type.');
        $this->assertSame([], $data->getProperties()[0]->type->namespace, 'namespace.');
        $this->assertSame(true, $data->getProperties()[0]->accessModifier->private, 'property name Modifiers.');
        $this->assertSame('price', $data->getProperties()[1]->name, 'property price.');
        $this->assertSame('Price', $data->getProperties()[1]->type->name, 'property price type.');
        $this->assertSame([], $data->getProperties()[1]->type->namespace, 'namespace.');
        $this->assertSame(true, $data->getProperties()[1]->accessModifier->private, 'property price Modifiers.');
    }

    public function testDump_with_namespace(): void {
        $options = new Options([]);
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->getClassType()->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->name, 'type.');
        $this->assertSame('Name', $data->getProperties()[0]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[0]->type->namespace, 'namespace.');
        $this->assertSame('price', $data->getProperties()[1]->name, 'name.');
        $this->assertSame('Price', $data->getProperties()[1]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[1]->type->namespace, 'namespace.');
    }

    public function testDump_with_phpdoc(): void {
        $options = new Options([]);
        $directory = sprintf('%s/phpdoc', $this->fixtureDir);
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->getClassType()->name, 'class type name.');
        $this->assertSame('Stmt_Class', $data->getClassType()->meta, 'class meta name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->name, 'type.');
        $this->assertSame('Name', $data->getProperties()[0]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[0]->type->namespace, 'Name namespace.');
        $this->assertSame('price', $data->getProperties()[1]->name, 'name.');
        $this->assertSame('Price', $data->getProperties()[1]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[1]->type->namespace, 'Price namespace.');
        $this->assertSame('Tag[]', $data->getProperties()[2]->type->name, 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[2]->type->namespace, 'Tag[] namespace.');
    }

    public function testDump_with_interface(): void {
        $options = new Options([]);
        $directory = sprintf('%s/interface', $this->fixtureDir);
        $filename = sprintf('%s/interface/product/Interface_.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Interface_', $data->getClassType()->name, 'class type name.');
        $this->assertSame('Stmt_Interface', $data->getClassType()->meta, 'class meta name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->name, 'property type.');
        $this->assertSame('string', $data->getProperties()[0]->type->name, 'property type.');
    }
    public function testDump_with_methods(): void {
        $options = new Options([]);
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->getClassType()->name, 'class type name.');
        $this->assertSame([], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('method1', $data->getMethods()[0]->name, 'namespace name.');
        $this->assertSame('param1', $data->getMethods()[0]->params[0]->name, 'parameter name.');
        $this->assertSame('Product', $data->getMethods()[0]->type->name, 'return type.');
        $this->assertSame([], $data->getMethods()[0]->type->namespace, 'return type namespace.');
        $this->assertSame(true, $data->getMethods()[0]->accessModifier->public, 'public.');
        $this->assertSame(false, $data->getMethods()[0]->accessModifier->private, 'private.');
        $this->assertSame(false, $data->getMethods()[0]->accessModifier->static, 'static.');
        $this->assertSame(false, $data->getMethods()[0]->accessModifier->protected, 'protected.');
    }
    public function testDump_with_methods2(): void {
        $options = new Options([]);
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->getClassType()->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('method1', $data->getMethods()[0]->name, 'namespace name.');
        $this->assertSame('Product', $data->getMethods()[0]->type->name, 'return type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->type->namespace, 'return type namespace.');
        $this->assertSame('param1', $data->getMethods()[0]->params[0]->name, 'parameter name.');
        $this->assertSame(true, $data->getMethods()[0]->accessModifier->public, 'public.');
        $this->assertSame(false, $data->getMethods()[0]->accessModifier->private, 'private.');
        $this->assertSame(false, $data->getMethods()[0]->accessModifier->static, 'static.');
        $this->assertSame(false, $data->getMethods()[0]->accessModifier->protected, 'protected.');
    }
    public function testDump_with_extend(): void {
        $options = new Options([]);
        $directory = sprintf('%s/extends', $this->fixtureDir);
        $filename = sprintf('%s/extends/product/Sub.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Sub', $data->getClassType()->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('Super', $data->getExtends()[0]->name, 'super class name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getExtends()[0]->namespace, 'super class namespace.');
    }
    public function testDump_with_implements(): void {
        $options = new Options([]);
        $directory = sprintf('%s/extends', $this->fixtureDir);
        $filename = sprintf('%s/extends/product/Implements_.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Implements_', $data->getClassType()->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('Interface_', $data->getExtends()[0]->name, 'super class name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getExtends()[0]->namespace, 'super class namespace.');
    }
    public function testGetUses(): void {
        $options = new Options([]);
        $directory = sprintf('%s/uses', $this->fixtureDir);
        $filename = sprintf('%s/uses/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->getClassType()->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[0]->namespace, 'use namespace.');
        $this->assertSame('Name', $data->getUses()[0]->name, 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[1]->namespace, 'use namespace.');
        $this->assertSame('Price', $data->getUses()[1]->name, 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product', 'tags'], $data->getUses()[2]->namespace, 'use namespace.');
        $this->assertSame('Tag', $data->getUses()[2]->name, 'use name.');
    }
    public function testGetUsesWithoutNamespace(): void {
        $options = new Options([]);
        $directory = sprintf('%s/uses', $this->fixtureDir);
        $filename = sprintf('%s/uses/product/ProductWithoutNamespace.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('ProductWithoutNamespace', $data->getClassType()->name, 'class type name.');
        $this->assertSame([], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[0]->namespace, 'use namespace.');
        $this->assertSame('Name', $data->getUses()[0]->name, 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[1]->namespace, 'use namespace.');
        $this->assertSame('Price', $data->getUses()[1]->name, 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[2]->namespace, 'use namespace.');
        $this->assertSame('Tag', $data->getUses()[2]->name, 'use name.');
    }
    public function testNullable(): void {
        $options = new Options([]);
        $directory = sprintf('%s/nullable', $this->fixtureDir);
        $filename = sprintf('%s/nullable/product/Product.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Product', $data->getClassType()->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('nullable', $data->getMethods()[0]->name, 'namespace name.');
        $this->assertSame('Tag', $data->getMethods()[0]->type->name, 'return type.');
        $this->assertSame(['hoge', 'fuga', 'product', 'tags'], $data->getMethods()[0]->type->namespace, 'return type namespace.');
        $this->assertSame('name', $data->getMethods()[0]->params[0]->name, 'parameter name.');
        $this->assertSame('Name', $data->getMethods()[0]->params[0]->type->name, 'parameter type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->params[0]->type->namespace, 'parameter type namespace.');
        $this->assertSame('tag', $data->getMethods()[0]->params[1]->name, 'parameter name. tag');
        $this->assertSame('Tag', $data->getMethods()[0]->params[1]->type->name, 'parameter type. tag');
        $this->assertSame(['hoge', 'fuga', 'product', 'tags'], $data->getMethods()[0]->params[1]->type->namespace, 'parameter type namespace. tag');
    }
    public function testNullableWithoutNamespace(): void {
        $options = new Options([]);
        $directory = sprintf('%s/nullable', $this->fixtureDir);
        $filename = sprintf('%s/nullable/product/ProductWithoutNamespace.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('ProductWithoutNamespace', $data->getClassType()->name, 'class type name.');
        $this->assertSame([], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('nullable', $data->getMethods()[0]->name, 'namespace name.');
        $this->assertSame('Name', $data->getMethods()[0]->type->name, 'return type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->type->namespace, 'return type namespace.');
        $this->assertSame('name', $data->getMethods()[0]->params[0]->name, 'parameter name.');
        $this->assertSame('Name', $data->getMethods()[0]->params[0]->type->name, 'parameter type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->params[0]->type->namespace, 'parameter type namespace.');
    }
    public function testFullyQualified(): void {
        $options = new Options([]);
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $filename = sprintf('%s/namespace/product/Exception.php', $this->fixtureDir);
        $class = new PhpReflection($directory, $filename, $options);

        $data = $class->getInfo();
        $this->assertSame('Exception', $data->getClassType()->name, 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, 'namespace name.');
        $this->assertSame('Exception', $data->getExtends()[0]->name, 'super class name.');
        $this->assertSame([], $data->getExtends()[0]->namespace, 'super class namespace.');
        $this->assertSame('getInner', $data->getMethods()[0]->name, 'method name.');
        $this->assertSame('Exception', $data->getMethods()[0]->type->name, 'return type.');
        $this->assertSame([], $data->getMethods()[0]->type->namespace, 'return type namespace.');
        $this->assertSame('e', $data->getMethods()[0]->params[0]->name, 'parameter name.');
        $this->assertSame('Exception', $data->getMethods()[0]->params[0]->type->name, 'parameter type.');
        $this->assertSame([], $data->getMethods()[0]->params[0]->type->namespace, 'parameter type namespace.');
        $this->assertSame('external', $data->getMethods()[1]->name, 'method name.');
        $this->assertSame('Exception', $data->getMethods()[1]->type->name, 'return type.');
        $this->assertSame(['external'], $data->getMethods()[1]->type->namespace, 'return type namespace.');
    }
//    public function testFullyQualified(): void {
//        $options = new Options([]);
//        $directory = sprintf('%s/classes', $this->fixtureDir);
//        $filename = sprintf('%s/classes/product/Product.php', $this->fixtureDir);
//        $parsed = PhpReflection::parseFile($directory, $filename, $options);
//
//        $data = $parsed[0]->getInfo();
//        $this->assertSame('Product', $data->getClassType()->name, '1st class type name.');
//        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, '1st namespace name.');
//        $data = $parsed[1]->getInfo();
//        $this->assertSame('Name', $data->getClassType()->name, '2nd class type name.');
//        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, '2nd namespace name.');
//        $data = $parsed[2]->getInfo();
//        $this->assertSame('Price', $data->getClassType()->name, '3rd class type name.');
//        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->namespace, '3rd namespace name.');
//    }
}
