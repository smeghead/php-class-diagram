<?php
namespace hoge\fuga\product;

use hoge\fuga\product\ {
    Name,
    Price,
};
use hoge\fuga\product\tag\Tag;

/**
 * @hoge hogehoge
 */
class Product {
    private ?string $nullableString;
    private int|string $intOrString;
    private Name $name;
    private Price $price;
    private \Exception $error;
    private bar\Boo $boo;
    private \hoge\fuga\product\bar\Boo $boo2;
    /** @var bur\Bon $docString */
    private bar\Boo $docString;
    /** @var string|int $docStringUnion */
    private $docStringUnion;
    /** @var string|bar\Bon $docStringUnion2 */
    private $docStringUnion2;

    /**
     * @param string|int $param1
     * @return int|null return val.
     */
    public function method1(int $paramInt, ?Price $price, string $param1): int {
        return 0;
    }

    /**
     * @return Product product (優先される情報)
     */
    public function method2(): int {
        return 0;
    }

    public function method3(): array {
        return [];
    }

    public function method4(Tag $tag): array {
        return [];
    }

    public function method5(Tag $tag): int|string {
        return 0;
    }
    /**
     * @return int|string return value.
     */
    public function method6(Tag $tag): int|string {
        return 0;
    }
    /**
     * @return Tag[] tags
     */
    public function method7(Tag $tag): array {
        return 0;
    }
}
