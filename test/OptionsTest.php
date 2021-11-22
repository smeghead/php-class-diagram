<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;

final class OptionsTest extends TestCase {
    public function setUp(): void {
    }

    public function testNothing(): void {
        $opt = [
        ];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertSame(false, $options->help(), 'help is off.');
    }
    public function testH(): void {
        $opt = [
            'h' => true,
        ];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertSame(true, $options->help(), 'help is on.');
    }
    public function testHelp(): void {
        $opt = [
            'help' => true,
        ];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertSame(true, $options->help(), 'help is on.');
    }
    public function testClassProperties1(): void {
        $opt = [
            'enable-class-properties' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(true, $options->classProperties(), 'classProperties is on.');
    }
    public function testClassProperties2(): void {
        $opt = [
            'disable-class-properties' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(false, $options->classProperties(), 'classProperties is off.');
    }
    public function testClassMethods1(): void {
        $opt = [
            'enable-class-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(true, $options->classMethods(), 'classMethods is on.');
    }
    public function testClassMethods2(): void {
        $opt = [
            'disable-class-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(false, $options->classMethods(), 'classMethods is off.');
    }
    public function testPhp1(): void {
        $opt = [
            'php7' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::PHP7, $options->phpVersion(), 'php version is 7.');
    }
    public function testPhp2(): void {
        $opt = [
            'php8' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::PHP8, $options->phpVersion(), 'php version is 8.');
    }
    public function testDiagram1(): void {
        $opt = [
            'class-diagram' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_CLASS, $options->diagram(), 'diagram is class.');
    }
    public function testDiagram2(): void {
        $opt = [
            'package-diagram' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_PACKAGE, $options->diagram(), 'diagram is package.');
    }
    public function testDiagram3(): void {
        $opt = [
        ];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_CLASS, $options->diagram(), 'default diagram is class.');
    }

}
