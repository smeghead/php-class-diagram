<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\Php\PhpEnumCase;
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class PhpReflectionTest extends TestCase
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
        $options = new Options([]);
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $this->assertNotNull($classes[0], 'initialize PhppReflection');
    }

    public function testDump(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame([], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->getName(), 'property name.');
        $this->assertSame('Name', $data->getProperties()[0]->getType()->getName(), 'property name type.');
        $this->assertSame([], $data->getProperties()[0]->getType()->getTypes()[0]->getNamespace(), 'namespace.');
        $this->assertSame(true, $data->getProperties()[0]->getAccessModifier()->isPrivate(), 'property name Modifiers.');
        $this->assertSame('price', $data->getProperties()[1]->getName(), 'property price.');
        $this->assertSame('Price', $data->getProperties()[1]->getType()->getName(), 'property price type.');
        $this->assertSame([], $data->getProperties()[1]->getType()->getTypes()[0]->getNamespace(), 'namespace.');
        $this->assertSame(true, $data->getProperties()[1]->getAccessModifier()->isPrivate(), 'property price Modifiers.');
    }

    public function testDump_Price(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $filename = sprintf('%s/no-namespace/product/Price.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Price', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame([], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('price', $data->getProperties()[0]->getName(), 'property price.');
        $this->assertSame('int', $data->getProperties()[0]->getType()->getName(), 'property price type.');
        $this->assertSame([], $data->getProperties()[0]->getType()->getTypes()[0]->getNamespace(), 'namespace.');
        $this->assertSame(true, $data->getProperties()[0]->getAccessModifier()->isPrivate(), 'property price Modifiers.');
    }
    public function testDump_php8_Price(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/php8', $this->fixtureDir);
        $filename = sprintf('%s/php8/product/Price.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Price', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('price8', $data->getProperties()[0]->getName(), 'property price.');
        $this->assertSame('int|float', $data->getProperties()[0]->getType()->getName(), 'property price type. php8 union type.');
        $this->assertSame([], $data->getProperties()[0]->getType()->getTypes()[0]->getNamespace(), 'namespace.');
        $this->assertSame(true, $data->getProperties()[0]->getAccessModifier()->isPrivate(), 'property price Modifiers.');
    }
    public function testDump_php8_constructer_properties(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/php8', $this->fixtureDir);
        $filename = sprintf('%s/php8/product/Price.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Price', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('field1', $data->getProperties()[1]->getName(), 'property field1.');
        $this->assertSame('int', $data->getProperties()[1]->getType()->getName(), 'property field1 type. php8 union type.');
        $this->assertSame([], $data->getProperties()[1]->getType()->getTypes()[0]->getNamespace(), 'namespace.');
        $this->assertSame(true, $data->getProperties()[1]->getAccessModifier()->isPrivate(), 'property field1 Modifiers.');
    }

    public function testDump_with_namespace(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->getName(), 'type.');
        $this->assertSame('Name', $data->getProperties()[0]->getType()->getName(), 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[0]->getType()->getTypes()[0]->getNamespace(), 'namespace.');
        $this->assertSame('price', $data->getProperties()[1]->getName(), 'name.');
        $this->assertSame('Price', $data->getProperties()[1]->getType()->getName(), 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[1]->getType()->getTypes()[0]->getNamespace(), 'namespace.');
    }

    public function testDump_with_phpdoc(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/phpdoc', $this->fixtureDir);
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame('Stmt_Class', $data->getClassType()->getMeta(), 'class meta name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->getName(), 'name.');
        $this->assertSame('Name', $data->getProperties()[0]->getType()->getName(), 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[0]->getType()->getTypes()[0]->getNamespace(), 'Name namespace.');
        $this->assertSame('price', $data->getProperties()[1]->getName(), 'name.');
        $this->assertSame('Price', $data->getProperties()[1]->getType()->getName(), 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[1]->getType()->getTypes()[0]->getNamespace(), 'Price namespace.');
        $this->assertSame('Tag[]', $data->getProperties()[2]->getType()->getName(), 'type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getProperties()[2]->getType()->getTypes()[0]->getNamespace(), 'Tag[] namespace.');
        $this->assertSame('Ban[]', $data->getProperties()[3]->getType()->getName(), 'full package name, Ban type.');
        $this->assertSame(['ban', 'ban', 'ban'], $data->getProperties()[3]->getType()->getTypes()[0]->getNamespace(), 'full package name, ban.ban.ban.Ban[] namespace.');
    }

    public function testDump_with_interface(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/interface', $this->fixtureDir);
        $filename = sprintf('%s/interface/product/Interface_.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Interface_', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame('Stmt_Interface', $data->getClassType()->getMeta(), 'class meta name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('name', $data->getProperties()[0]->getName(), 'property type.');
        $this->assertSame('string', $data->getProperties()[0]->getType()->getName(), 'property type.');
    }
    public function testDump_with_methods(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $filename = sprintf('%s/no-namespace/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame([], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('method1', $data->getMethods()[0]->getName(), 'namespace name.');
        $this->assertSame('param1', $data->getMethods()[0]->getParams()[0]->getName(), 'parameter name.');
        $this->assertSame('Product', $data->getMethods()[0]->getType()->getName(), 'return type.');
        $this->assertSame([], $data->getMethods()[0]->getType()->getTypes()[0]->getNamespace(), 'return type namespace.');
        $this->assertSame(true, $data->getMethods()[0]->getAccessModifier()->isPublic(), 'public.');
        $this->assertSame(false, $data->getMethods()[0]->getAccessModifier()->isPrivate(), 'private.');
        $this->assertSame(false, $data->getMethods()[0]->getAccessModifier()->isStatic(), 'static.');
        $this->assertSame(false, $data->getMethods()[0]->getAccessModifier()->isProtected(), 'protected.');
    }
    public function testDump_with_methods2(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $filename = sprintf('%s/namespace/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('method1', $data->getMethods()[0]->getName(), 'namespace name.');
        $this->assertSame('Product', $data->getMethods()[0]->getType()->getName(), 'return type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->getType()->getTypes()[0]->getNamespace(), 'return type namespace.');
        $this->assertSame('param1', $data->getMethods()[0]->getParams()[0]->getName(), 'parameter name.');
        $this->assertSame(true, $data->getMethods()[0]->getAccessModifier()->isPublic(), 'public.');
        $this->assertSame(false, $data->getMethods()[0]->getAccessModifier()->isPrivate(), 'private.');
        $this->assertSame(false, $data->getMethods()[0]->getAccessModifier()->isStatic(), 'static.');
        $this->assertSame(false, $data->getMethods()[0]->getAccessModifier()->isProtected(), 'protected.');
    }
    public function testDump_with_extend(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/extends', $this->fixtureDir);
        $filename = sprintf('%s/extends/product/Sub.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Sub', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('Super', $data->getExtends()[0]->getName(), 'super class name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getExtends()[0]->getNamespace(), 'super class namespace.');
    }
    public function testDump_with_implements(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/extends', $this->fixtureDir);
        $filename = sprintf('%s/extends/product/Implements_.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Implements_', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('Interface_', $data->getExtends()[0]->getName(), 'super class name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getExtends()[0]->getNamespace(), 'super class namespace.');
    }
    public function testGetUses(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/uses', $this->fixtureDir);
        $filename = sprintf('%s/uses/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[0]->getNamespace(), 'use namespace.');
        $this->assertSame('Name', $data->getUses()[0]->getName(), 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[1]->getNamespace(), 'use namespace.');
        $this->assertSame('Price', $data->getUses()[1]->getName(), 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product', 'tags'], $data->getUses()[2]->getNamespace(), 'use namespace.');
        $this->assertSame('Tag', $data->getUses()[2]->getName(), 'use name.');
    }
    public function testGetUsesWithoutNamespace(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/uses', $this->fixtureDir);
        $filename = sprintf('%s/uses/product/ProductWithoutNamespace.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('ProductWithoutNamespace', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame([], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[0]->getNamespace(), 'use namespace.');
        $this->assertSame('Name', $data->getUses()[0]->getName(), 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[1]->getNamespace(), 'use namespace.');
        $this->assertSame('Price', $data->getUses()[1]->getName(), 'use name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getUses()[2]->getNamespace(), 'use namespace.');
        $this->assertSame('Tag', $data->getUses()[2]->getName(), 'use name.');
    }
    public function testNullable(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/nullable', $this->fixtureDir);
        $filename = sprintf('%s/nullable/product/Product.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('nullable', $data->getMethods()[0]->getName(), 'namespace name.');
        $this->assertSame('?Tag', $data->getMethods()[0]->getType()->getName(), 'return type.');
        $this->assertSame(['hoge', 'fuga', 'product', 'tags'], $data->getMethods()[0]->getType()->getTypes()[0]->getNamespace(), 'return type namespace.');
        $this->assertSame('name', $data->getMethods()[0]->getParams()[0]->getName(), 'parameter name.');
        $this->assertSame('?Name', $data->getMethods()[0]->getParams()[0]->getType()->getName(), 'parameter type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->getParams()[0]->getType()->getTypes()[0]->getNamespace(), 'parameter type namespace.');
        $this->assertSame('tag', $data->getMethods()[0]->getParams()[1]->getName(), 'parameter name. tag');
        $this->assertSame('Tag', $data->getMethods()[0]->getParams()[1]->getType()->getName(), 'parameter type. tag');
        $this->assertSame(['hoge', 'fuga', 'product', 'tags'], $data->getMethods()[0]->getParams()[1]->getType()->getTypes()[0]->getNamespace(), 'parameter type namespace. tag');
    }
    public function testNullableWithoutNamespace(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/nullable', $this->fixtureDir);
        $filename = sprintf('%s/nullable/product/ProductWithoutNamespace.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('ProductWithoutNamespace', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame([], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('nullable', $data->getMethods()[0]->getName(), 'namespace name.');
        $this->assertSame('?Name', $data->getMethods()[0]->getType()->getName(), 'return type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->getType()->getTypes()[0]->getNamespace(), 'return type namespace.');
        $this->assertSame('name', $data->getMethods()[0]->getParams()[0]->getName(), 'parameter name.');
        $this->assertSame('?Name', $data->getMethods()[0]->getParams()[0]->getType()->getName(), 'parameter type.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getMethods()[0]->getParams()[0]->getType()->getTypes()[0]->getNamespace(), 'parameter type namespace.');
    }
    public function testFullyQualified(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $filename = sprintf('%s/namespace/product/Exception.php', $this->fixtureDir);
        $classes = PhpReader::parseFile($directory, $filename, $options);

        $data = $classes[0]->getInfo();
        $this->assertSame('Exception', $data->getClassType()->getName(), 'class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), 'namespace name.');
        $this->assertSame('Exception', $data->getExtends()[0]->getName(), 'super class name.');
        $this->assertSame([], $data->getExtends()[0]->getNamespace(), 'super class namespace.');
        $this->assertSame('getInner', $data->getMethods()[0]->getName(), 'method name.');
        $this->assertSame('Exception', $data->getMethods()[0]->getType()->getName(), 'return type.');
        $this->assertSame([], $data->getMethods()[0]->getType()->getTypes()[0]->getNamespace(), 'return type namespace.');
        $this->assertSame('e', $data->getMethods()[0]->getParams()[0]->getName(), 'parameter name.');
        $this->assertSame('Exception', $data->getMethods()[0]->getParams()[0]->getType()->getName(), 'parameter type.');
        $this->assertSame([], $data->getMethods()[0]->getParams()[0]->getType()->getTypes()[0]->getNamespace(), 'parameter type namespace.');
        $this->assertSame('external', $data->getMethods()[1]->getName(), 'method name.');
        $this->assertSame('Exception', $data->getMethods()[1]->getType()->getName(), 'return type.');
        $this->assertSame(['external'], $data->getMethods()[1]->getType()->getTypes()[0]->getNamespace(), 'return type namespace.');
    }
    public function testClassesInAFile(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/classes', $this->fixtureDir);
        $filename = sprintf('%s/classes/product/Product.php', $this->fixtureDir);
        $parsed = PhpReader::parseFile($directory, $filename, $options);

        $data = $parsed[0]->getInfo();
        $this->assertSame('Product', $data->getClassType()->getName(), '1st class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), '1st namespace name.');
        $data = $parsed[1]->getInfo();
        $this->assertSame('Name', $data->getClassType()->getName(), '2nd class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), '2nd namespace name.');
        $data = $parsed[2]->getInfo();
        $this->assertSame('Price', $data->getClassType()->getName(), '3rd class type name.');
        $this->assertSame(['hoge', 'fuga', 'product'], $data->getClassType()->getNamespace(), '3rd namespace name.');
    }
    public function testTrait(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/trait', $this->fixtureDir);
        $filename = sprintf('%s/trait/TestTrait.php', $this->fixtureDir);
        $parsed = PhpReader::parseFile($directory, $filename, $options);

        $data = $parsed[0]->getInfo();
        $this->assertSame('TestTrait', $data->getClassType()->getName(), '1st class type name.');
        $this->assertSame(['Foo', 'Traits'], $data->getClassType()->getNamespace(), '1st namespace name.');
        $this->assertSame([], $data->getMethods()[0]->getType()->getTypes()[0]->getNamespace(), 'return type namespace.');
        $this->assertSame('bool', $data->getMethods()[0]->getType()->getTypes()[0]->getName(), 'return type name.');
        $this->assertSame('name', $data->getMethods()[0]->getParams()[0]->getName(), 'parameter name.');
        $this->assertSame('string', $data->getMethods()[0]->getParams()[0]->getType()->getName(), 'parameter type.');
        $this->assertSame([], $data->getMethods()[0]->getParams()[0]->getType()->getTypes()[0]->getNamespace(), 'parameter type namespace.');
    }
    /**
     * @requires PHP >= 8.1
     * PHP8.0 dose not have `enum`. 
     */
    public function testEnum(): void
    {
        $options = new Options([]);
        $directory = sprintf('%s/enum', $this->fixtureDir);
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        $parsed = PhpReader::parseFile($directory, $filename, $options);

        $data = $parsed[0]->getInfo();
        $this->assertSame('Suit', $data->getClassType()->getName(), '1st class type name.');
        $this->assertSame(['Hoge', 'TestEnum'], $data->getClassType()->getNamespace(), '1st namespace name.');
        $enums = $data->getEnumCases();
        $this->assertSame(['Hearts', 'Diamonds', 'Clubs', 'Spades'], array_map(function (PhpEnumCase $e) {
            return $e->getName();
        }, $enums), 'enum cases.');
        $this->assertSame(['ハート', 'ダイヤ', 'クローバー', 'スペード'], array_map(function (PhpEnumCase $e) {
            return $e->getDocString();
        }, $enums), 'enum cases.');
    }
}
