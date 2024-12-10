<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\{
    Relation,
    Entry,
    Package,
};
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class PackageTest extends TestCase
{
    private string $fixtureDir;

    public function setUp(): void
    {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function tearDown(): void
    {
        $this->fixtureDir = '';

        parent::tearDown();
    }

    public function testInitialize(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $namespace = $rel->getPackage();

        $this->assertInstanceOf(Package::class, $namespace, 'namespace instance');
        $this->assertSame('ROOT', $namespace->name, 'ROOT namespace name');

        $product = $namespace->children[0];
        $this->assertSame('product', $product->name, 'product namespace name');

        $this->assertSame('Product', $product->entries[0]->getClass()->getClassType()->getName(), 'product class name');
        $this->assertSame('Price', $product->entries[1]->getClass()->getClassType()->getName(), 'price class name');
        $this->assertSame('Name', $product->entries[2]->getClass()->getClassType()->getName(), 'name class name');
    }

    public function testDump(): void
    {
        $directory = sprintf('%s/namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product
    class "Price" as product_Price
    class "Name" as product_Name
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
  product_Product ..> product_Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump2(): void
    {
        $directory = sprintf('%s/sub-namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/utility/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product
    class "Price" as product_Price
    package utility as product.utility {
      class "Name" as product_utility_Name
    }
  }
  product_Product ..> product_Price
  product_Product ..> product_utility_Name
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump3(): void
    {
        $directory = sprintf('%s/interface', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Interface_.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump4(): void
    {
        $directory = sprintf('%s/interface', $this->fixtureDir);
        $options = new Options([
            'enable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Interface_.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_ {
      -name : string
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump5(): void
    {
        $directory = sprintf('%s/interface', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'enable-class-methods' => true,
        ]);
        $files = [
            'product/Interface_.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_ {
      +method1(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump6(): void
    {
        $directory = sprintf('%s/interface', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'enable-class-methods' => true,
        ]);
        $files = [
            'product/Interface_.php',
            'product/Implement_.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_ {
      +method1(param1)
    }
    class "Implement_" as product_Implement_ {
      +method1(param1)
    }
  }
  product_Interface_ <|-- product_Implement_
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump7(): void
    {
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product
    class "Price" as product_Price
    class "Name" as product_Name
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
  product_Product ..> product_Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump_paths_with_hyphens_To_avoid_PlantUML_errors(): void
    {
        $directory = sprintf('%s/paths-with-hyphens', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product-with-hyphens/Product.php',
            'product-with-hyphens/Price.php',
            'product-with-hyphens/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product-with-hyphens as product-with-hyphens {
    class "Product" as productwithhyphens_Product
    class "Price" as productwithhyphens_Price
    class "Name" as productwithhyphens_Name
  }
  productwithhyphens_Product ..> productwithhyphens_Name
  productwithhyphens_Product ..> productwithhyphens_Price
  productwithhyphens_Product ..> productwithhyphens_Product
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDumpPackage1(): void
    {
        $directory = sprintf('%s/no-namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Product.php',
            'product/Price.php',
            'product/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml package-related-diagram
  package ROOT as ROOT {
    package product {
    }
  }
  package PhpParse #DDDDDD {
  }
  product --> PhpParse
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dumpPackages()), 'output PlantUML script.');
    }
    public function testDumpPackage_bothSideArrows(): void
    {
        $directory = sprintf('%s/sub-namespace', $this->fixtureDir);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $files = [
            'product/Product.php',
            'product/utility/Name.php',
        ];

        $rel = $this->getRelation($files, $directory, $options);

        $expected = <<<EOS
@startuml package-related-diagram
  package hoge.fuga as fuga {
    package product {
      package utility {
      }
    }
  }
  product <-[#red,plain,thickness=4]-> utility
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dumpPackages()), 'output PlantUML script.');
    }

    /**
     * @param string[] $files
     */
    private function getRelation(array $files, string $directory, Options $options): Relation
    {
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            $entries[] = array_map(fn($c) => new Entry(dirname($f), $c->getInfo(), $options), $classes);
        }

        return new Relation(array_merge(...$entries), $options);
    }
}
