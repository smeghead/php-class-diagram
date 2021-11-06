<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\ {
    Options,
    Relation,
};
use Smeghead\PhpClassDiagram\DiagramElement\ {
    Entry,
    Namespace_,
};

final class Namespace_Test extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
    }

    private string $product_expression = '{"type":{"name":"Product","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"name","type":{"name":"Name","namespace":[]}},{"name":"price","type":{"name":"Price","namespace":[]}}]}';
    private string $price_expression = '{"type":{"name":"Price","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"price","type":{"name":"int","namespace":[]}}]}';
    private string $name_expression = '{"type":{"name":"Name","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"name","type":{"name":"string","namespace":[]}}]}';
    private string $interface_expression = '{"type":{"name":"Interface_","meta":"Stmt_Interface","namespace":[]},"properties":[{"name":"name","type":{"name":"string","namespace":[]}}]}';

    public function testInitialize(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', json_decode($this->product_expression), $options),
            new Entry('product', json_decode($this->price_expression), $options),
            new Entry('product', json_decode($this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
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
        $options = new Options([]);
        $entries = [
            new Entry('product', json_decode($this->product_expression), $options),
            new Entry('product', json_decode($this->price_expression), $options),
            new Entry('product', json_decode($this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

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
        $options = new Options([]);
        $entries = [
            new Entry('product', json_decode($this->product_expression), $options),
            new Entry('product', json_decode($this->price_expression), $options),
            new Entry('product/utility', json_decode($this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
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

    public function testDump3(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', json_decode($this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml
  package "product" <<Rectangle>> {
    interface Interface_
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump4(): void {
        $options = new Options(['enable-class-properties' => true]);
        $entries = [
            new Entry('product', json_decode($this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml
  package "product" <<Rectangle>> {
    interface Interface_ {
      string name
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
}
