<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Relation;
use Smeghead\PhpClassDiagram\DiagramElement\Entry;

final class RelationTest extends TestCase {
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

        $this->assertNotNull($rel, 'initialize Relation');
    }

}
