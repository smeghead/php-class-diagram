<?php
namespace hoge\fuga\product;

use hoge\fuga\product\ {
    Name,
    Price,
};

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
    /** @var string|int $docString */
    private $docStringUnion;

    /**
     * @param string|int $param1  
     * @return Product product
     */
    public function method1(string $param1) {
    }
}
