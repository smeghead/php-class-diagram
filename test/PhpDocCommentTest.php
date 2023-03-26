<?php declare(strict_types=1);

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Php\Doc\PhpDocComment;

final class PhpDocCommentTest extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function testDocString(): void {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        // var_dump($ast[0]->stmts[0]);
        $enum = $ast[0]->stmts[0];
        $doc = new PhpDocComment($enum);

        $this->assertSame('スート', $doc->getText(), 'comment string');
    }
    public function test_enum_getDescription(): void {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        // var_dump($ast[0]->stmts[0]);
        $enum = $ast[0]->stmts[0];
        $doc = new PhpDocComment($enum);

        $this->assertSame('スート', $doc->getDescription(), 'description');
    }

    public function testDocStringMultiLines(): void {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        // var_dump($ast[0]->stmts[0]);
        $enumCase = $ast[0]->stmts[0]->stmts[3];
        $doc = new PhpDocComment($enumCase);

        $this->assertSame("スペード\n説明コメント", $doc->getText(), 'multiline comment string');
    }

    public function test_getDescription(): void {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/enum/TestEnum.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        // var_dump($ast[0]->stmts[0]);
        $enumCase = $ast[0]->stmts[0]->stmts[3];
        $doc = new PhpDocComment($enumCase);

        $this->assertSame("スペード", $doc->getDescription(), 'description string');
    }

    public function test_getVarType(): void {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/phpdoc/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        // var_dump($ast[0]->stmts[1]->stmts[0]);
        $var = $ast[0]->stmts[1]->stmts[0];
        $doc = new PhpDocComment($var);

        $this->assertSame("Name", $doc->getVarTypeName(), 'var type name.');
    }
    public function test_getParamType(): void {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        // var_dump($ast[0]->stmts[2]->stmts[10]);
        $method = $ast[0]->stmts[2]->stmts[10];
        $doc = new PhpDocComment($method);

        $this->assertSame("string|int", $doc->getParamTypeName('param1'), 'param type name.');
    }
    public function test_getReturnType(): void {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $filename = sprintf('%s/php8/product/Product.php', $this->fixtureDir);
        try {
            $ast = $parser->parse(file_get_contents($filename));
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        // var_dump($ast[0]->stmts[2]->stmts[10]);
        $method = $ast[0]->stmts[2]->stmts[10];
        $doc = new PhpDocComment($method);

        $this->assertSame("int|null", $doc->getReturnTypeName(), 'return type name.');
    }
}
