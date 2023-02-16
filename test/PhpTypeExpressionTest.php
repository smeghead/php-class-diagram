<?php declare(strict_types=1);

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Php\PhpType;
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

        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[0], ['hoge', 'fuga', 'product'], []);
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
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[1], ['hoge', 'fuga', 'product'], []);
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
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[2], ['hoge', 'fuga', 'product'], []);
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
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[4], ['hoge', 'fuga', 'product'], []);
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
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[5], ['hoge', 'fuga', 'product'], []);
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
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[6], ['hoge', 'fuga', 'product'], []);
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
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[7], ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'bur'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Bon', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testDocStringUnion(): void {
        // /** @var string|int $docStringUnion */
        // private $docStringUnion;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[8], ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testDocStringUnion2(): void {
        // /** @var string|bar\Bon $docStringUnion2 */
        // private $docStringUnion2;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $expression = PhpTypeExpression::buildByVar($ast[0]->stmts[2]->stmts[9], ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame(['hoge', 'fuga', 'product', 'bar'], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('Bon', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodParameterInt(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        // var_dump($ast[0]->stmts[1]->stmts[8]);die();
        $doc = $ast[0]->stmts[2]->stmts[10]->getDocComment();
        $docString = $doc->getText();
        $param = $ast[0]->stmts[2]->stmts[10]->getParams()[0];
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $docString, 'paramint', []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodParameterPrice(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        // var_dump($ast[0]->stmts[1]->stmts[8]);die();
        $doc = $ast[0]->stmts[2]->stmts[10]->getDocComment();
        $docString = $doc->getText();
        $param = $ast[0]->stmts[2]->stmts[10]->getParams()[1];
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $docString, 'paramint', []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Price', $types[0]->getName(), 'name');
        $this->assertSame(true, $types[0]->getNullable(), 'nullable');
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
        $doc = $ast[0]->stmts[2]->stmts[10]->getDocComment();
        $docString = $doc->getText();
        $param = $ast[0]->stmts[2]->stmts[10]->getParams()[2];
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $docString, 'param1', []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('string', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame([], $types[1]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodReturnInt(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $method = $ast[0]->stmts[2]->stmts[10];
        // var_dump($method);die();
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodReturnProduct(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $method = $ast[0]->stmts[2]->stmts[11];
        // var_dump($method);die();
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Product', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodReturnArray(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $method = $ast[0]->stmts[2]->stmts[12];
        // var_dump($method);die();
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('array', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodParameterTag(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        // var_dump($ast[0]->stmts[1]->stmts[8]);die();
        $docString = '';
        $param = $ast[0]->stmts[2]->stmts[13]->getParams()[0];
        $uses = [new PhpType(['hoge', 'fuga', 'product', 'tag'], '', 'Tag')];
        $expression = PhpTypeExpression::buildByMethodParam($param, ['hoge', 'fuga', 'product'], $docString, 'paramint', $uses);
        $types = $expression->getTypes();

        $this->assertSame(['hoge', 'fuga', 'product', 'tag'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Tag', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }
    public function testMethodReturnUnion(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $method = $ast[0]->stmts[2]->stmts[14];
        //  var_dump($method);die();
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame('method5', $method->name->name, 'method name');
        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame('string', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodReturnUnionDoc(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $method = $ast[0]->stmts[2]->stmts[15];
        //  var_dump($method);die();
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], []);
        $types = $expression->getTypes();

        $this->assertSame('method6', $method->name->name, 'method name');
        $this->assertSame([], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('int', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
        $this->assertSame('string', $types[1]->getName(), 'name');
        $this->assertSame(false, $types[1]->getNullable(), 'nullable');
    }
    public function testMethodReturnObjectArray(): void {
        // /** @params string|int $param1 */
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }
        $method = $ast[0]->stmts[2]->stmts[16];
        $uses = [new PhpType(['hoge', 'fuga', 'product', 'tag'], '', 'Tag')];
        //  var_dump($method);die();
        $expression = PhpTypeExpression::buildByMethodReturn($method, ['hoge', 'fuga', 'product'], $uses);
        $types = $expression->getTypes();

        $this->assertSame('method7', $method->name->name, 'method name');
        $this->assertSame(['hoge', 'fuga', 'product', 'tag'], $types[0]->getNamespace(), 'namespace');
        $this->assertSame('Tag[]', $types[0]->getName(), 'name');
        $this->assertSame(false, $types[0]->getNullable(), 'nullable');
    }

}
