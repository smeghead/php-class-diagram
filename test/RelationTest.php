<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\ {
    Relation,
    Entry,
};

final class RelationTest extends TestCase {
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

        $this->assertNotNull($rel, 'initialize Relation');
    }

}
