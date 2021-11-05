<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Relation;
use Smeghead\PhpClassDiagram\DiagramElement\ {
    Entry,
    Namespace_,
};

final class Namespace_Test extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
    }

    private string $product_expression = '{"type":{"name":"Product","namespace":[]},"properties":[{"name":"name","type":{"name":"Name","namespace":[]}},{"name":"price","type":{"name":"Price","namespace":[]}}]}';
    private string $price_expression = '{"type":{"name":"Price","namespace":[]},"properties":[{"name":"price","type":{"name":"int","namespace":[]}}]}';
    private string $name_expression = '{"type":{"name":"Name","namespace":[]},"properties":[{"name":"name","type":{"name":"string","namespace":[]}}]}';

    public function testInitialize(): void {
        $entries = [
            new Entry('product', json_decode($this->product_expression)),
            new Entry('product', json_decode($this->price_expression)),
            new Entry('product', json_decode($this->name_expression)),
        ];
        $rel = new Relation($entries);
        $namespace = $rel->getNamespace();

        $this->assertInstanceOf(Namespace_::class, $namespace, 'namespace instance');
        $this->assertSame('ROOT', $namespace->name, 'ROOT namespace name');

        $product = $namespace->children[0];
        $this->assertSame('product', $product->name, 'product namespace name');

        $this->assertSame('Product', $product->entries[0]->info->type->name, 'product class name');
        $this->assertSame('Price', $product->entries[1]->info->type->name, 'product class name');
        $this->assertSame('Name', $product->entries[2]->info->type->name, 'product class name');

    }

    public function testDump(): void {
        $entries = [
            new Entry('product', json_decode($this->product_expression)),
            new Entry('product', json_decode($this->price_expression)),
            new Entry('product', json_decode($this->name_expression)),
        ];
        $rel = new Relation($entries);

        $expected =<<<EOS
@startuml
  package "product" <<Rectangle>> {
    class Product
    class Price
    class Name
  }
  Product ..> Name
  Product ..> Price
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump2(): void {
        $entries = [
            new Entry('product', json_decode($this->product_expression)),
            new Entry('product', json_decode($this->price_expression)),
            new Entry('product/utility', json_decode($this->name_expression)),
        ];
        $rel = new Relation($entries);
        $expected =<<<EOS
@startuml
  package "product" <<Rectangle>> {
    class Product
    class Price
    package "utility" <<Rectangle>> {
      class Name
    }
  }
  Product ..> Name
  Product ..> Price
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
}
