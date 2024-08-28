<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\Entry;
use Smeghead\PhpClassDiagram\DiagramElement\Relation;
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class ClassDiagramClassNameSummaryTest extends TestCase
{
    private string $fixtureDir;
    public function setUp(): void
    {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->fixtureDir = '';

        parent::tearDown();
    }

    public function testClassnameWithSummary(): void
    {
        $options = new Options([
            'enable-class-name-summary' => true
        ]);

        $entries = $this->getEntries($options);

        $rel = new Relation($entries, $options);
        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product\\n<b>製品</b>" as product_Product {
      -name : Name
      -price : Price
      +method1(param1)
    }
    class "Price\\n<b>価格</b>" as product_Price {
      -price : int
    }
    class "Name\\n<b>製品名</b>" as product_Name {
      -name : string
    }
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
  product_Product ..> product_Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testClassnameWithoutSummary(): void
    {
        $options = new Options([
            'disable-class-name-summary' => true
        ]);

        $entries = $this->getEntries($options);

        $rel = new Relation($entries, $options);
        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product {
      -name : Name
      -price : Price
      +method1(param1)
    }
    class "Price" as product_Price {
      -price : int
    }
    class "Name" as product_Name {
      -name : string
    }
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
  product_Product ..> product_Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    /**
     * @return array<Entry>
     */
    private function getEntries(Options $options): array
    {
        $directory = sprintf('%s/classname-summary', $this->fixtureDir);
        $filename = sprintf('%s/classname-summary/product/Product.php', $this->fixtureDir);
        $entries[] = new Entry('product', PhpReader::parseFile($directory, $filename, $options)[0]->getInfo(), $options);
        $filename = sprintf('%s/classname-summary/product/Price.php', $this->fixtureDir);
        $entries[] = new Entry('product', PhpReader::parseFile($directory, $filename, $options)[0]->getInfo(), $options);
        $filename = sprintf('%s/classname-summary/product/Name.php', $this->fixtureDir);
        $entries[] = new Entry('product', PhpReader::parseFile($directory, $filename, $options)[0]->getInfo(), $options);

        return $entries;
    }
}
