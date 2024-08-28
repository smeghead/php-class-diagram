<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\DiagramElement\ExternalPackage\PackageHierarchy;

final class PackageHierarchyTest extends TestCase
{
    public function testEmptyExternalPackages(): void
    {
        $sut = new PackageHierarchy([]);

        $this->assertSame('', $sut->dump(), 'empty string');
    }

    public function testOnlyExternalPackage(): void
    {
        $externals = [
            'External.One',
        ];

        $sut = new PackageHierarchy($externals);

        $expected = <<<EOC
  package External #DDDDDD {
    package One #DDDDDD {
    }
  }
EOC;
        $this->assertSame($expected, $sut->dump(), 'empty string');
    }
}
