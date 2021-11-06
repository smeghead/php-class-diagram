<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\ {
    Options,
    Relation,
};
use Smeghead\PhpClassDiagram\DiagramElement\Entry;

final class RelationTest extends TestCase {
    private $fixtureDir;
    public function setUp(): void {
    }

    private string $product_expression = '{"type":{"name":"Product","namespace":[]},"properties":[{"name":"name","type":{"name":"Name","namespace":[]}},{"name":"price","type":{"name":"Price","namespace":[]}}]}';
    private string $price_expression = '{"type":{"name":"Price","namespace":[]},"properties":[{"name":"price","type":{"name":"int","namespace":[]}}]}';
    private string $name_expression = '{"type":{"name":"Name","namespace":[]},"properties":[{"name":"name","type":{"name":"string","namespace":[]}}]}';

    private string $product_with_tags_expression = '{"type":{"name":"Product","namespace":[]},"properties":[{"name":"name","type":{"name":"Name","namespace":[]}},{"name":"price","type":{"name":"Price","namespace":[]}},{"name":"tags","type":{"name":"Tag[]","namespace":[]}}]}';
    private string $tag_expression = '{"type":{"name":"Tag","namespace":[]},"properties":[{"name":"name","type":{"name":"string","namespace":[]}}]}';

    public function testInitialize(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', json_decode($this->product_expression), $options),
            new Entry('product', json_decode($this->price_expression), $options),
            new Entry('product', json_decode($this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

        $this->assertNotNull($rel, 'initialize Relation');
    }

    public function testGetRelations1(): void {
        $options = new Options([]);
        $entries = [
            new Entry('product', json_decode($this->product_expression), $options),
            new Entry('product', json_decode($this->price_expression), $options),
            new Entry('product', json_decode($this->name_expression), $options),
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
            new Entry('product', json_decode($this->product_with_tags_expression), $options),
            new Entry('product', json_decode($this->price_expression), $options),
            new Entry('product', json_decode($this->name_expression), $options),
            new Entry('product', json_decode($this->tag_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $relations = $rel->getRelations();

        $this->assertSame(3, count($relations), 'count');
        $this->assertSame('  Product "1" ..> "*" Tag', $relations[0], 'relation 1');
        $this->assertSame('  Product ..> Name', $relations[1], 'relation 2');
        $this->assertSame('  Product ..> Price', $relations[2], 'relation 3');
    }
}
