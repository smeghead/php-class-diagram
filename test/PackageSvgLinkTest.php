<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\{
    Relation,
    Entry,
};
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class PackageSvgLinkTest extends TestCase
{
    private string $fixtureDir;

    public function setUp(): void
    {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function tearDown(): void
    {
        $this->fixtureDir = '';
    }

    public function testDump_SvgLink_Classes(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
            'svg-topurl' => 'https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/namespace',
        ]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];

        $rel = $this->getRelation($directory, $options, $files);

        $expected = <<<EOS
@startuml class-diagram
  skinparam svgLinkTarget _blank
  skinparam topurl https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/namespace
  package product as product {
    class "Product" as product_Product [[/product/Product.php Product Class]]
    class "Price" as product_Price [[/product/Price.php Price Class]]
    class "Name" as product_Name [[/product/Name.php Name Class]]
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
  product_Product ..> product_Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump_SvgLink_Classes_toplevel_php_file(): void
    {
        $directory = sprintf('%s/namespace/product', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
            'svg-topurl' => 'https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/namespace/product',
        ]);
        $files = [
            'Product.php',
            'Price.php',
            'Name.php',
        ];

        $rel = $this->getRelation($directory, $options, $files);

        $expected = <<<EOS
@startuml class-diagram
  skinparam svgLinkTarget _blank
  skinparam topurl https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/namespace/product
  class "Product" as Product [[/Product.php Product Class]]
  class "Price" as Price [[/Price.php Price Class]]
  class "Name" as Name [[/Name.php Name Class]]
  Product ..> Name
  Product ..> Price
  Product ..> Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump_SvgLink_Interfaces(): void
    {
        $directory = sprintf('%s/interface', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'enable-class-methods' => true,
            'svg-topurl' => 'https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/interface',
        ]);
        $files = [
            'product/Interface_.php',
        ];

        $rel = $this->getRelation($directory, $options, $files);

        $expected = <<<EOS
@startuml class-diagram
  skinparam svgLinkTarget _blank
  skinparam topurl https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/interface
  package product as product {
    interface "Interface_" as product_Interface_ [[/product/Interface_.php Interface_ Interface]] {
      +method1(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump_SvgLink_ClassesWithoutNamespace(): void
    {
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
            'svg-topurl' => 'https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/no-namespace',
        ]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];

        $rel = $this->getRelation($directory, $options, $files);

        $expected = <<<EOS
@startuml class-diagram
  skinparam svgLinkTarget _blank
  skinparam topurl https://github.com/smeghead/php-class-diagram/tree/main/test/fixtures/no-namespace
  package product as product {
    class "Product" as product_Product [[/product/Product.php Product Class]]
    class "Price" as product_Price [[/product/Price.php Price Class]]
    class "Name" as product_Name [[/product/Name.php Name Class]]
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
  product_Product ..> product_Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    /**
     * @param string[] $files
     */
    private function getRelation(string $directory, Options $options, array $files): Relation
    {
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            $d = dirname($f);
            if ($d === '.') {
                $d = '';
            }
            $entries[] = array_map(fn($c) => new Entry($d, $c->getInfo(), $options), $classes);
        }

        return new Relation(array_merge(...$entries), $options);
    }
}
