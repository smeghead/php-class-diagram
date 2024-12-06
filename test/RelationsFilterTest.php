<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\RelationsFilter;

final class RelationsFilterTest extends TestCase
{
    /**
     * @var array|string[]
     */
    private array $fixture;

    public function setUp(): void
    {
        $this->fixture = [
            '  Entry "1" ..> "*" Arrow',
            '  Entry "1" ..> "*" Arrow',
            '  Package "1" ..> "*" Package',
            '  Package "1" ..> "*" Entry',
            '  Package ..> Entry',
            '  Package "1" ..> "*" Arrow',
            '  Package "1" ..> "*" Entry',
            '  Package ..> Package',
            '  PackageRelations ..> Package',
            '  PackageRelations ..> Package',
            '  Relation ..> Package',
            '  Relation ..> RelationsFilter',
            '  Relation "1" ..> "*" Entry',
            '  Relation ..> Package',
            '  Arrow <|-- ArrowDependency',
            '  Arrow <|-- ArrowInheritance',
            '  ExternalPackage_PackageHierarchy ..> ExternalPackage_PackageNode',
            '  ExternalPackage_PackageNode "1" ..> "*" ExternalPackage_PackageNode',
            '  ExternalPackage_PackageNode "1" ..> "*" ExternalPackage_PackageNode',
            '  ExternalPackage_PackageHierarchy ..> ExternalPackage_PackageNode',
            '  ExternalPackage_PackageNode ..> ExternalPackage_PackageNode',
            '  Entry ..> Division_DivisionColor',
            '  Entry ..> ArrowDependency',
            '  Entry ..> ArrowDependency',
            '  Entry ..> ArrowDependency',
            '  Entry ..> ArrowInheritance',
            '  Entry ..> ArrowDependency',
            '  Package ..> Entry',
            '  Package ..> Package',
            '  Package ..> Package',
            '  Package ..> Package',
            '  PackageRelations ..> Package',
            '  PackageRelations ..> ExternalPackage_PackageHierarchy',
            '  PackageRelations ..> PackageArrow',
            '  PackageRelations ..> PackageArrow',
            '  Relation ..> RelationsFilter',
            '  Relation ..> Package',
            '  Relation ..> Package',
            '  Relation ..> Arrow',
            '  Relation ..> PackageRelations',
        ];
    }

    public function testFiltersInboundRelations(): void
    {
        $relationsFilter = new RelationsFilter(new Options([
            'rel-target-to' => 'PackageNode'
        ]));

        $result = $relationsFilter->filterRelations($this->fixture);

        $this->assertSame("  ExternalPackage_PackageHierarchy ..> ExternalPackage_PackageNode", $result[0]);
        $this->assertSame("  ExternalPackage_PackageNode \"1\" ..> \"*\" ExternalPackage_PackageNode", $result[1]);
        $this->assertSame("  ExternalPackage_PackageNode ..> ExternalPackage_PackageNode", $result[2]);
        $this->assertSame("  PackageRelations ..> ExternalPackage_PackageHierarchy", $result[3]);
        $this->assertSame("  Relation ..> PackageRelations", $result[4]);
    }

    public function testFiltersTargetRelations(): void
    {
        $relationsFilter = new RelationsFilter(new Options([
            'rel-target' => 'Entry'
        ]));

        $result = $relationsFilter->filterRelations($this->fixture);

        $this->assertSame("  Entry \"1\" ..> \"*\" Arrow", $result[0]);
        $this->assertSame("  Entry ..> Division_DivisionColor", $result[1]);
        $this->assertSame("  Entry ..> ArrowDependency", $result[2]);
        $this->assertSame("  Entry ..> ArrowInheritance", $result[3]);
        $this->assertSame("  Package \"1\" ..> \"*\" Package", $result[4]);
        $this->assertSame("  Package \"1\" ..> \"*\" Entry", $result[5]);
        $this->assertSame("  Package ..> Entry", $result[6]);
        $this->assertSame("  Package ..> Package", $result[7]);
        $this->assertSame("  PackageRelations ..> Package", $result[8]);
        $this->assertSame("  Relation ..> Package", $result[9]);
        $this->assertSame("  Relation \"1\" ..> \"*\" Entry", $result[10]);
        $this->assertSame("  Relation ..> PackageRelations", $result[11]);
    }

    public function testFiltersInboundRelationsWithDepth(): void
    {
        $relationsFilter = new RelationsFilter(new Options([
            'rel-target-to' => 'PackageNode',
            'rel-target-depth' => 1
        ]));

        $result = $relationsFilter->filterRelations($this->fixture);

        $this->assertSame("  ExternalPackage_PackageHierarchy ..> ExternalPackage_PackageNode", $result[0]);
        $this->assertSame("  ExternalPackage_PackageNode \"1\" ..> \"*\" ExternalPackage_PackageNode", $result[1]);
        $this->assertSame("  ExternalPackage_PackageNode ..> ExternalPackage_PackageNode", $result[2]);
        $this->assertCount(3, $result);
    }

    public function testFiltersOutboundRelations(): void
    {
        $relationsFilter = new RelationsFilter(new Options([
            'rel-target-from' => 'Package'
        ]));

        $result = $relationsFilter->filterRelations($this->fixture);

        $this->assertSame("  Entry \"1\" ..> \"*\" Arrow", $result[0]);
        $this->assertSame("  Package \"1\" ..> \"*\" Package", $result[1]);
        $this->assertSame("  Package \"1\" ..> \"*\" Entry", $result[2]);
        $this->assertSame("  Package ..> Entry", $result[3]);
        $this->assertSame("  Package \"1\" ..> \"*\" Arrow", $result[4]);
        $this->assertSame("  Package ..> Package", $result[5]);
        $this->assertSame("  Entry ..> Division_DivisionColor", $result[6]);
        $this->assertSame("  Entry ..> ArrowDependency", $result[7]);
        $this->assertSame("  Entry ..> ArrowInheritance", $result[8]);
    }

    public function testFiltersOutboundRelationsWithDepth(): void
    {
        $relationsFilter = new RelationsFilter(new Options([
            'rel-target-from' => 'Package',
            'rel-target-depth' => 1
        ]));

        $result = $relationsFilter->filterRelations($this->fixture);

        $this->assertSame("  Package \"1\" ..> \"*\" Package", $result[0]);
        $this->assertSame("  Package \"1\" ..> \"*\" Entry", $result[1]);
        $this->assertSame("  Package ..> Entry", $result[2]);
        $this->assertSame("  Package \"1\" ..> \"*\" Arrow", $result[3]);
        $this->assertSame("  Package ..> Package", $result[4]);
    }

    public function testGeneratesRemoveUnlinkedDirective(): void
    {
        $relationsFilter = new RelationsFilter(new Options([
            'rel-target-from' => 'Package'
        ]));

        $relationsFilter->filterRelations($this->fixture);
        $result = $relationsFilter->addRemoveUnlinkedDirective([]);

        $this->assertSame("  remove @unlinked", $result[0]);
    }
}
