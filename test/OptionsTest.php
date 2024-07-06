<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;

final class OptionsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testNothing(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertSame(false, $options->help(), 'help is off.');
    }

    public function testH(): void
    {
        $opt = [
            'h' => true,
        ];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertSame(true, $options->help(), 'help is on.');
    }

    public function testHelp(): void
    {
        $opt = [
            'help' => true,
        ];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertSame(true, $options->help(), 'help is on.');
    }

    public function testClassProperties1(): void
    {
        $opt = [
            'enable-class-properties' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(true, $options->classProperties(), 'classProperties is on.');
    }

    public function testClassProperties2(): void
    {
        $opt = [
            'disable-class-properties' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(false, $options->classProperties(), 'classProperties is off.');
    }

    public function testClassProperties3(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertSame(true, $options->classProperties(), 'classProperties is default on.');
    }

    public function testClassMethods1(): void
    {
        $opt = [
            'enable-class-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(true, $options->classMethods(), 'classMethods is on.');
    }

    public function testClassMethods2(): void
    {
        $opt = [
            'disable-class-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(false, $options->classMethods(), 'classMethods is off.');
    }

    public function testClassMethods3(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertSame(true, $options->classMethods(), 'classMethods is default on.');
    }

    public function testClassNameSummary1(): void
    {
        $opt = [
            'enable-class-name-summary' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(true, $options->classNameSummary(), 'classNameSummary is on.');
    }

    public function testClassNameSummary2(): void
    {
        $opt = [
            'disable-class-name-summary' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(false, $options->classNameSummary(), 'classNameSummary is off.');
    }

    public function testClassNameSummary3(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertSame(true, $options->classNameSummary(), 'classNameSummary is default on.');
    }

    public function testPhp1(): void
    {
        $opt = [
            'php7' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::PHP7, $options->phpVersion(), 'php version is 7.');
    }

    public function testPhp2(): void
    {
        $opt = [
            'php8' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::PHP8, $options->phpVersion(), 'php version is 8.');
    }

    public function testDiagram1(): void
    {
        $opt = [
            'class-diagram' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_CLASS, $options->diagram(), 'diagram is class.');
    }

    public function testDiagram2(): void
    {
        $opt = [
            'package-diagram' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_PACKAGE, $options->diagram(), 'diagram is package.');
    }

    public function testDiagram_division(): void
    {
        $opt = [
            'division-diagram' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_DIVISION, $options->diagram(), 'diagram is division.');
    }

    public function testDiagram3(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_CLASS, $options->diagram(), 'default diagram is class.');
    }

    public function testDiagram4(): void
    {
        $opt = [
            'jig-diagram' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(Options::DIAGRAM_JIG, $options->diagram(), 'diagram is jig.');
    }

    public function testHeader(): void
    {
        $opt = [
            'header' => 'title PHP Class Diagram',
        ];

        $options = new Options($opt);

        $this->assertSame('title PHP Class Diagram', $options->headers()[0], 'specified header.');
    }

    public function testMultipleHeaders(): void
    {
        $opt = [
            'header' => [
                'title PHP Class Diagram',
                'skinparam pageMargin 10',
            ],
        ];

        $options = new Options($opt);

        $this->assertSame('title PHP Class Diagram', $options->headers()[0], 'specified header. title');
        $this->assertSame('skinparam pageMargin 10', $options->headers()[1], 'specified header. pageMargin');
    }

    public function testInclude(): void
    {
        $opt = [
            'include' => '*.php8',
        ];

        $options = new Options($opt);

        $this->assertSame('*.php8', $options->includes()[0], 'specified include.');
    }

    public function testMultipleInclude(): void
    {
        $opt = [
            'include' => [
                '*.php7',
                '*.php8',
            ],
        ];

        $options = new Options($opt);

        $this->assertSame('*.php7', $options->includes()[0], 'specified include. php7');
        $this->assertSame('*.php8', $options->includes()[1], 'specified include. php8');
    }

    public function testIncludeDefault(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertSame('*.php', $options->includes()[0], 'default include.');
    }

    public function testexclude(): void
    {
        $opt = [
            'exclude' => '*Exception.php',
        ];

        $options = new Options($opt);

        $this->assertSame('*Exception.php', $options->excludes()[0], 'specified exclude.');
    }

    public function testMultipleExclude(): void
    {
        $opt = [
            'exclude' => [
                '*Exception.php',
                'config.php',
            ],
        ];

        $options = new Options($opt);

        $this->assertSame('*Exception.php', $options->excludes()[0], 'specified exclude. *Exception.php');
        $this->assertSame('config.php', $options->excludes()[1], 'specified exclude. config.php');
    }

    public function testExcludeDefault(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertSame(0, count($options->excludes()), 'default exclude is empty.');
    }

    public function testSvgTopurlDefault(): void
    {
        $opt = [];

        $options = new Options($opt);

        $this->assertSame('*.php', $options->includes()[0], 'default include.');
    }

    public function testSvgTopurl(): void
    {
        $opt = [
            'svg-topurl' => 'https://github.com/smeghead/php-class-diagram',
        ];

        $options = new Options($opt);

        $this->assertSame('https://github.com/smeghead/php-class-diagram', $options->svgTopurl(), 'specified svg-topurl.');
    }

    public function testHidePrivateDefault(): void
    {
        $opt = [
        ];

        $options = new Options($opt);

        $this->assertSame(false, $options->hidePrivateProperties(), 'default hide-private-property.');
        $this->assertSame(false, $options->hidePrivateMethods(), 'default hide-private-methods.');
    }

    public function testHidePrivateTrue(): void
    {
        $opt = [
            'hide-private' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(true, $options->hidePrivateProperties(), 'hide-private-property is true.');
        $this->assertSame(true, $options->hidePrivateMethods(), 'hide-private-methods is true.');
    }

    public function testHidePrivateMethodsTrue(): void
    {
        $opt = [
            'hide-private-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertSame(false, $options->hidePrivateProperties(), 'hide-private-property is false.');
        $this->assertSame(true, $options->hidePrivateMethods(), 'hide-private-methods is true.');
    }

}
