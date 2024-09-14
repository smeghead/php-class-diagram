<?php

declare(strict_types=1);

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Php\Doc\PhpDocComment;

final class PhpDocCommentTest extends TestCase
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

    public function testDocString(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $enum = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Enum_ && $node->name->toString() === 'Suit';
        });

        $doc = new PhpDocComment($enum);

        $this->assertSame('スート', $doc->getText(), 'comment string');
    }
    public function test_enum_getDescription(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $enum = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Enum_ && $node->name->toString() === 'Suit';
        });
        $doc = new PhpDocComment($enum);

        $this->assertSame('スート', $doc->getDescription(), 'description');
    }

    public function testDocStringMultiLines(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $enum = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Enum_ && $node->name->toString() === 'Suit';
        });
        $enumCase = $finder->findFirst($enum, function(Node $node){
            return $node instanceof EnumCase && $node->name->toString() === 'Spades';
        });
        $doc = new PhpDocComment($enumCase);

        $this->assertSame("スペード\n説明コメント", $doc->getText(), 'multiline comment string');
    }

    public function test_getDescription(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $enum = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Enum_ && $node->name->toString() === 'Suit';
        });
        $enumCase = $finder->findFirst($enum, function(Node $node){
            return $node instanceof EnumCase && $node->name->toString() === 'Spades';
        });

        $doc = new PhpDocComment($enumCase);

        $this->assertSame("スペード", $doc->getDescription(), 'description string');
    }

    public function test_getVarType(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $var = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'name';
        });

        $doc = new PhpDocComment($var);

        $this->assertSame("Name", $doc->getVarTypeName(), 'var type name.');
    }
    public function test_getParamType(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method1';
        });
        $doc = new PhpDocComment($method);

        $this->assertSame("string|int", $doc->getParamTypeName('param1'), 'param type name.');
    }
    public function test_getReturnType(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method1';
        });

        $doc = new PhpDocComment($method);

        $this->assertSame("int|null", $doc->getReturnTypeName(), 'return type name.');
    }
    public function test_getVarType_array_expression(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/array-expression-in-doc/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $var = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'tags';
        });

        $doc = new PhpDocComment($var);

        $this->assertSame("array<Tag>", $doc->getVarTypeName(), 'var type name.');
    }
    public function test_getReturnType_array_expression(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/array-expression-in-doc/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'getTags';
        });

        $doc = new PhpDocComment($method);

        $this->assertSame("array<Tag>", $doc->getReturnTypeName(), 'return type name.');
    }
    public function test_getClassComment(): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $class = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Class_ && $node->name->toString() === 'Product';
        });

        $doc = new PhpDocComment($class);

        $this->assertSame('', $doc->getDescription(), 'class type name.');
    }
}
