<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\{
    Relation,
    Entry,
};
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class HidePrivateTest extends TestCase
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

    public function testDefault(): void
    {
        $directory = sprintf('%s/hide-private', $this->fixtureDir);
        $options = new Options([]);
        $rel = $this->getRelation($directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product {
      +name : string
      -memo : string
      +publicFunction(param1)
      -privateFunction(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testHidePrivate(): void
    {
        $directory = sprintf('%s/hide-private', $this->fixtureDir);
        $options = new Options([
            'hide-private' => true,
        ]);
        $rel = $this->getRelation($directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product {
      +name : string
      +publicFunction(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testHidePrivateProperties(): void
    {
        $directory = sprintf('%s/hide-private', $this->fixtureDir);
        $options = new Options([
            'hide-private-properties' => true,
        ]);
        $rel = $this->getRelation($directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product {
      +name : string
      +publicFunction(param1)
      -privateFunction(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testHidePrivateMethods(): void
    {
        $directory = sprintf('%s/hide-private', $this->fixtureDir);
        $options = new Options([
            'hide-private-methods' => true,
        ]);
        $rel = $this->getRelation($directory, $options);

        $expected = <<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product {
      +name : string
      -memo : string
      +publicFunction(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    private function getRelation(string $directory, Options $options): Relation
    {
        $files = [
            'product/Product.php',
        ];

        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/hide-private/%s', $this->fixtureDir, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            $entries[] = array_map(fn($c) => new Entry(dirname($f), $c->getInfo(), $options), $classes);
        }

        return new Relation(array_merge(...$entries), $options);
    }
}
