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
        $options = new Options([]);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertfalse($options->help(), 'help is off.');
    }

    public function testH(): void
    {
        $opt = [
            'h' => true,
        ];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertTrue($options->help(), 'help is on.');
    }

    public function testHelp(): void
    {
        $opt = [
            'help' => true,
        ];

        $options = new Options($opt);

        $this->assertNotNull($options, 'initialize Options');
        $this->assertTrue($options->help(), 'help is on.');
    }

    public function testClassPropertiesEnabled(): void
    {
        $opt = [
            'enable-class-properties' => true,
        ];

        $options = new Options($opt);

        $this->assertTrue($options->classProperties(), 'classProperties is on.');
    }

    public function testClassPropertiesDisabled(): void
    {
        $opt = [
            'disable-class-properties' => true,
        ];

        $options = new Options($opt);

        $this->assertFalse($options->classProperties(), 'classProperties is off.');
    }

    public function testClassPropertiesDefault(): void
    {
        $options = new Options([]);

        $this->assertTrue($options->classProperties(), 'classProperties is default on.');
    }

    public function testClassMethodsEnabled(): void
    {
        $opt = [
            'enable-class-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertTrue($options->classMethods(), 'classMethods is on.');
    }

    public function testClassMethodsDisabled(): void
    {
        $opt = [
            'disable-class-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertFalse($options->classMethods(), 'classMethods is off.');
    }

    public function testClassMethodsDefault(): void
    {
        $options = new Options([]);

        $this->assertTrue($options->classMethods(), 'classMethods is default on.');
    }

    public function testClassNameSummaryEnabled(): void
    {
        $opt = [
            'enable-class-name-summary' => true,
        ];

        $options = new Options($opt);

        $this->assertTrue($options->classNameSummary(), 'classNameSummary is on.');
    }

    public function testClassNameSummaryDisabled(): void
    {
        $opt = [
            'disable-class-name-summary' => true,
        ];

        $options = new Options($opt);

        $this->assertFalse($options->classNameSummary(), 'classNameSummary is off.');
    }

    public function testClassNameSummaryDefault(): void
    {
        $options = new Options([]);

        $this->assertTrue($options->classNameSummary(), 'classNameSummary is default on.');
    }

    /**
     * @dataProvider provideDiagrams
     */
    public function testDiagrams(array $options, string $expected): void
    {
        $options = new Options($options);

        $this->assertSame($expected, $options->diagram(), sprintf('diagram is %s.', $expected));
    }

    public function provideDiagrams(): array
    {
        return [
            [[], Options::DIAGRAM_CLASS],// default
            [['class-diagram' => true], Options::DIAGRAM_CLASS],
            [['package-diagram' => true], Options::DIAGRAM_PACKAGE],
            [['division-diagram' => true], Options::DIAGRAM_DIVISION],
            [['jig-diagram' => true], Options::DIAGRAM_JIG],
        ];
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
        $options = new Options([]);

        $this->assertSame('*.php', $options->includes()[0], 'default include.');
    }

    public function testExclude(): void
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
        $options = new Options([]);

        $this->assertCount(0, $options->excludes(), 'default exclude is empty.');
    }

    public function testSvgTopUrlDefault(): void
    {
        $options = new Options([]);

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
        $options = new Options([]);

        $this->assertFalse($options->hidePrivateProperties(), 'default hide-private-property.');
        $this->assertFalse($options->hidePrivateMethods(), 'default hide-private-methods.');
    }

    public function testHidePrivateTrue(): void
    {
        $opt = [
            'hide-private' => true,
        ];

        $options = new Options($opt);

        $this->assertTrue($options->hidePrivateProperties(), 'hide-private-property is true.');
        $this->assertTrue($options->hidePrivateMethods(), 'hide-private-methods is true.');
    }

    public function testHidePrivateMethodsTrue(): void
    {
        $opt = [
            'hide-private-methods' => true,
        ];

        $options = new Options($opt);

        $this->assertFalse($options->hidePrivateProperties(), 'hide-private-property is false.');
        $this->assertTrue($options->hidePrivateMethods(), 'hide-private-methods is true.');
    }

    public function testRelTarget(): void
    {
        $opt = [
            'rel-target' => 'Product,Name',
        ];

        $options = new Options($opt);

        $this->assertSame(['Product', 'Name'], $options->relTargetsFrom());
        $this->assertSame(['Product', 'Name'], $options->relTargetsTo());
    }

    public function testRelTargetFrom(): void
    {
        $opt = [
            'rel-target-from' => 'Product,Name',
        ];

        $options = new Options($opt);

        $this->assertSame(['Product', 'Name'], $options->relTargetsFrom());
        $this->assertSame([], $options->relTargetsTo());
    }

    public function testRelTargetTo(): void
    {
        $opt = [
            'rel-target-to' => 'Product,Name',
        ];

        $options = new Options($opt);

        $this->assertSame([], $options->relTargetsFrom());
        $this->assertSame(['Product', 'Name'], $options->relTargetsTo());
    }

    public function testRelTargetDepth(): void
    {
        $opt = [
            'rel-target-depth' => '1',
        ];

        $options = new Options($opt);

        $this->assertSame(1, $options->relTargetsDepth());
    }
}
