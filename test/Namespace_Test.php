<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\ {
    Relation,
    Entry,
    Namespace_,
};

final class Namespace_Test extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
    }

    private string $product_expression = '{"name":"Product","namespace":"","properties":[{"name":"name","type":"Name","private":true},{"name":"price","type":"Price","private":true}]}';
    private string $price_expression = '{"name":"Price","namespace":"","properties":[{"name":"price","type":"int","private":true}]}';
    private string $name_expression = '{"name":"Name","namespace":"","properties":[{"name":"name","type":"string","private":true}]}';

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

        $this->assertSame('Product', $product->entries[0]->info->name, 'product class name');
        $this->assertSame('Price', $product->entries[1]->info->name, 'product class name');
        $this->assertSame('Name', $product->entries[2]->info->name, 'product class name');

    }

    public function testDump(): void {
        $entries = [
            new Entry('product', json_decode($this->product_expression)),
            new Entry('product', json_decode($this->price_expression)),
            new Entry('product', json_decode($this->name_expression)),
        ];
        $rel = new Relation($entries);
        //まだ矢印を出力する実装はしてない。
//        $expected =<<<EOS
//@startuml
//package "product" <<Rectangle>> {
//  class Product
//  class Price
//  class Name
//}
//Product ..> Price
//Product ..> Name
//@enduml
//EOS;
        $expected =<<<EOS
@startuml
  package "product" <<Rectangle>> {
    class Product
    class Price
    class Name
  }
@enduml
EOS;
        $this->assertSame($expected, implode("\r\n", $rel->dump()), 'output PlantUML script.');
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
@enduml
EOS;
        $this->assertSame($expected, implode("\r\n", $rel->dump()), 'output PlantUML script.');
    }
}
