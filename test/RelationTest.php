<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\ {
    Options,
    Relation,
};
use Smeghead\PhpClassDiagram\DiagramElement\Entry;

require_once(__DIR__ . '/dummy/PhpClassDummy.php');

final class RelationTest extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
    }

    private string $product_expression = '{"type":{"name":"Product","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"name","type":{"name":"Name","namespace":[]},"modifier":{"private":true}},{"name":"price","type":{"name":"Price","namespace":[]},"modifier":{"private":true}}]}';
    private string $price_expression = '{"type":{"name":"Price","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"price","type":{"name":"int","namespace":[]},"modifier":{"private":true}}]}';
    private string $name_expression = '{"type":{"name":"Name","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"name","type":{"name":"string","namespace":[]},"modifier":{"private":true}}]}';

    private string $product_with_tags_expression = '{"type":{"name":"Product","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"name","type":{"name":"Name","namespace":[]},"modifier":{"private":true}},{"name":"price","type":{"name":"Price","namespace":[]},"modifier":{"private":true}},{"name":"tags","type":{"name":"Tag[]","namespace":[]},"modifier":{"private":true}}]}';
    private string $tag_expression = '{"type":{"name":"Tag","meta":"Stmt_Class","namespace":[]},"properties":[{"name":"name","type":{"name":"string","namespace":[]},"modifier":{"private":true}}]}';

    public function testInitialize(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy($this->product_expression), $options),
            new Entry('product', new PhpClassDummy($this->price_expression), $options),
            new Entry('product', new PhpClassDummy($this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

        $this->assertNotNull($rel, 'initialize Relation');
    }

    public function testGetRelations1(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy($this->product_expression), $options),
            new Entry('product', new PhpClassDummy($this->price_expression), $options),
            new Entry('product', new PhpClassDummy($this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $relations = $rel->getRelations();

        $this->assertSame(2, count($relations), 'count');
        $this->assertSame('  Product ..> Name', $relations[0], 'relation 1');
        $this->assertSame('  Product ..> Price', $relations[1], 'relation 2');
    }

    public function testGetRelations2(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', new PhpClassDummy($this->product_with_tags_expression), $options),
            new Entry('product', new PhpClassDummy($this->price_expression), $options),
            new Entry('product', new PhpClassDummy($this->name_expression), $options),
            new Entry('product', new PhpClassDummy($this->tag_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $relations = $rel->getRelations();

        $this->assertSame(3, count($relations), 'count');
        $this->assertSame('  Product "1" ..> "*" Tag', $relations[0], 'relation 1');
        $this->assertSame('  Product ..> Name', $relations[1], 'relation 2');
        $this->assertSame('  Product ..> Price', $relations[2], 'relation 3');
    }
}
