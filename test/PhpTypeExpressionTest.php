<?php declare(strict_types=1);

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Php\PhpTypeExpression;

final class PhpTypeExpressionTest extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function testNullableString(): void {
        //     private ?string $nullableString;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[0], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(true, $types[0]->getNullable(), 'nullable');
    }
    public function testIntOrString(): void {
        //     private int|string $intOrString;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[1], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testPrice(): void {
        // private Price $price;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[2], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Name', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testException(): void {
        // private \Exception $error;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[4], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Exception', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testRelated(): void {
        // private bar\Boo $boo;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[5], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'bar'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Boo', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testAbsolute(): void {
        // private \hoge\fuga\product\bar\Boo $boo2;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[6], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'bar'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Boo', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testDocString(): void {
        // /** @var bur\Bon $docString */
        // private bar\Boo $docString;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[7], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'bur'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Bon', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testDocStringUnion(): void {
        // /** @var bur\Bon $docString */
        // private bar\Boo $docString;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[1]->stmts[8], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodParameterDocString(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        // var_dump($ast[0]->stmts[1]->stmts[8]);die();
        $expression = PhpTypeExpression::buildByMethodParam($ast[0]->stmts[1]->stmts[9], ['hoge', 'fuga', 'product']);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }

}
