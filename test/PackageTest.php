<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\ {
    Relation,
    Entry,
    Package,
};
require_once(__DIR__ . '/dummy/PhpClassDummy.php');

final class PackageTest extends TestCase {
    private $fixtureDir;

    private string $product_expression = <<<EOJ
{
    "type": {
        "name": "Product",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [
        {
            "name": "name",
            "type": {
                "name": "Name",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        },
        {
            "name": "price",
            "type": {
                "name": "Price",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;
    private string $price_expression = <<<EOJ
{
    "type": {
        "name": "Price",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [
        {
            "name": "price",
            "type": {
                "name": "int",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;
    private string $name_expression = <<<EOJ
{
    "type": {
        "name": "Name",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [
        {
            "name": "name",
            "type": {
                "name": "string",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;
    private string $interface_expression = <<<EOJ
{
    "type": {
        "name":"Interface_",
        "meta":"Stmt_Interface",
        "namespace":[]
    },
    "properties":[
        {
            "name":"name",
            "type":{
                "name":"string",
                "namespace":[]
            },
            "modifier":{"private":true}
        }
    ],
    "methods":[
        {
            "name":"method1",
            "type":{
                "name":"Product",
                "namespace":[]
            },
            "params":[
                {
                    "name":"param1",
                    "type":{"name":"string"}
                }
            ],
            "modifier":{"private":true}
        }
    ]
}
EOJ;
    private string $implement_expression = <<<EOJ
{
    "type":{
        "name":"Implement_",
        "meta":"Stmt_Class",
        "namespace":[]
    },
    "properties":[
        {
            "name":"name",
            "type":{"name":"string","namespace":[]},
            "modifier":{"private":true}
        }
    ],
    "methods":[
        {
            "name":"method1",
            "type":{
                "name":"Product",
                "namespace":[]
            },
            "params":[
                {
                    "name":"param1",
                    "type":{"name":"string"}
                }
            ],
            "modifier":{"private":true}
        }
    ],
    "extends":[
        {
            "name":"Interface_",
            "meta":"Stmt_Interface",
            "namespace":[]
        }
    ]
}
EOJ;
    private string $product_method_expression = <<<EOJ
{
    "type": {
        "name": "Product",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [],
    "methods":[
        {
            "name":"getName",
            "type":{
                "name":"Name",
                "namespace":[]
            },
            "params":[
            ],
            "modifier":{"public":true}
        },
        {
            "name":"getPrice",
            "type":{
                "name":"Price",
                "namespace":[]
            },
            "params":[
            ],
            "modifier":{"private":true}
        }
    ]
}
EOJ;

    public function setUp(): void {
    }

    public function testInitialize(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $namespace = $rel->getNamespace();

        $this->assertInstanceOf(Package::class, $namespace, 'namespace instance');
        $this->assertSame('ROOT', $namespace->name, 'ROOT namespace name');

        $product = $namespace->children[0];
        $this->assertSame('product', $product->name, 'product namespace name');

        $this->assertSame('Product', $product->entries[0]->class->getClassType()->name, 'product class name');
        $this->assertSame('Price', $product->entries[1]->class->getClassType()->name, 'price class name');
        $this->assertSame('Name', $product->entries[2]->class->getClassType()->name, 'name class name');

    }

    public function testDump(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

        $expected =<<<EOS
@startuml
  package product as product <<Rectangle>> {
    class product.Product
    class product.Price
    class product.Name
  }
  product.Product ..> product.Name
  product.Product ..> product.Price
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump2(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product/utility', new PhpClassDummy('product/utility', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml
  package product as product <<Rectangle>> {
    class product.Product
    class product.Price
    package utility as product.utility <<Rectangle>> {
      class product.Name
    }
  }
  product.Product ..> product.Name
  product.Product ..> product.Price
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump3(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml
  package product as product <<Rectangle>> {
    interface product.Interface_
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump4(): void {
        $options = new Options(['enable-class-properties' => true]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml
  package product as product <<Rectangle>> {
    interface product.Interface_ {
      -name : string
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump5(): void {
        $options = new Options(['enable-class-methods' => true]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml
  package product as product <<Rectangle>> {
    interface product.Interface_ {
      -method1(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump6(): void {
        $options = new Options(['enable-class-methods' => true]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Implement_.php', $this->implement_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml
  package product as product <<Rectangle>> {
    interface product.Interface_ {
      -method1(param1)
    }
    class product.Implement_ {
      -method1(param1)
    }
  }
  product.Interface_ <|-- product.Implement_
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump7(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_method_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

        $expected =<<<EOS
@startuml
  package product as product <<Rectangle>> {
    class product.Product
    class product.Price
    class product.Name
  }
  product.Product ..> product.Name
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
}
