<?php

declare(strict_types=1);

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Php\PhpType;
use Smeghead\PhpClassDiagram\Php\PhpTypeExpression;

final class PhpTypeExpressionTest extends TestCase
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

    public function testNullableString(): void
    {
        //     private ?string $nullableString;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'nullableString';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(true, $types[0]->getNullable(), 'nullable');
    }
    public function testIntOrString(): void
    {
        //     private int|string $intOrString;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'intOrString';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testName(): void
    {
        // private Price $price;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'name';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Name', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testException(): void
    {
        // private \Exception $error;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'error';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Exception', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testRelated(): void
    {
        // private bar\Boo $boo;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'boo';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'bar'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Boo', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testAbsolute(): void
    {
        // private \hoge\fuga\product\bar\Boo $boo2;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'boo2';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'bar'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Boo', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testDocString(): void
    {
        // /** @var bur\Bon $docString */
        // private bar\Boo $docString;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'docString';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'bur'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Bon', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testDocStringUnion(): void
    {
        // /** @var string|int $docStringUnion */
        // private $docStringUnion;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'docStringUnion';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testDocStringUnion2(): void
    {
        // /** @var string|bar\Bon $docStringUnion2 */
        // private $docStringUnion2;
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'docStringUnion2';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame(['hoge', 'fuga', 'product', 'bar'], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('Bon', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodParameterInt(): void
    {
        // int $paramInt
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
        $param = $finder->findFirst($method, function (Node $node) {
            return $node instanceof Param && $node->var->name === 'paramInt';
        });

        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $method, 'paramint', []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodParameterPrice(): void
    {
        // ?Price $price
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
        $param = $finder->findFirst($method, function (Node $node) {
            return $node instanceof Param && $node->var->name === 'price';
        });
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $method, 'paramint', []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Price', $types[0]->getName(), 'name');
        $this->assertSame(true, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodParameterDocString(): void
    {
        // /** @params string|int $param1 */
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
        $param = $finder->findFirst($method, function (Node $node) {
            return $node instanceof Param && $node->var->name === 'param1';
        });
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $method, 'param1', []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodReturnInt(): void
    {
        // /** @return int|null return val. */
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
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodReturnProduct(): void
    {
        // /** @return Product product (優先される情報) */
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method2';
        });
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Product', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodReturnArray(): void
    {
        // public function method3(): array
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method3';
        });
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('array', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodParameterTag(): void
    {
        // public function method4(Tag $tag): array
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method4';
        });
        $param = $finder->findFirst($method, function (Node $node) {
            return $node instanceof Param && $node->var->name === 'tag';
        });
        $uses = [new PhpType(['hoge', 'fuga', 'product', 'tag'], '', 'Tag')];
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $method, 'paramint', $uses);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'tag'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Tag', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodReturnUnion(): void
    {
        // public function method5(Tag $tag): int|string
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method5';
        });
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame('method5', $method->name->name, 'method name');
        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame('string', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodReturnUnionDoc(): void
    {
        // /** @return int|string return value. */
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method6';
        });
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame('method6', $method->name->name, 'method name');
        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame('string', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodReturnObjectArray(): void
    {
        // /** @return Tag[] tags */
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'method7';
        });
        $uses = [new PhpType(['hoge', 'fuga', 'product', 'tag'], '', 'Tag')];
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], $uses);
        $types = $expression->getTypes();

        $this->assertSame('method7', $method->name->name, 'method name');
        $this->assertSame(['hoge', 'fuga', 'product', 'tag'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Tag[]', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testVarArrayAlternative(): void
    {
        // /** @var array<int, Tag> 付与されたタグ一覧 */
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $target = $finder->findFirst($ast, function(Node $node){
            return $node instanceof Property && $node->props[0]->name->toString() === 'alternativeTags';
        });
        $expression = PhpTypeExpression::buildByVar($target, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('array<int, Tag>', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');

    }
    public function testMethodParameterAltaernativeTag(): void
    {
        // /**
        //  * @param array<int, Tag> $tags tags
        //  * @return array<int, Tag> tags
        //  */
        // public function arrayTags(array $tags): array
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'arrayTags';
        });
        $param = $finder->findFirst($method, function (Node $node) {
            return $node instanceof Param && $node->var->name === 'tags';
        });
        $uses = [new PhpType(['hoge', 'fuga', 'product'], '', 'Tag')];
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $method, 'tags', $uses);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('array<int, Tag>', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodReturnAltaernativeTag(): void
    {
        // /**
        //  * @param array<int, Tag> $tags tags
        //  * @return array<int, Tag> tags
        //  */
        // public function arrayTags(array $tags): array
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $finder = new NodeFinder();
        $method = $finder->findFirst($ast, function(Node $node){
            return $node instanceof ClassMethod && $node->name->toString() === 'arrayTags';
        });
        $param = $finder->findFirst($method, function (Node $node) {
            return $node instanceof Param && $node->var->name === 'tags';
        });
        $uses = [new PhpType(['hoge', 'fuga', 'product'], '', 'Tag')];
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], $uses);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('array<int, Tag>', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
}
