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

    /** @return Product product */
    public function method1(string $param1) {
    }
}