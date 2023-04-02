<?php
namespace hoge\fuga\product;

use hoge\fuga\product\ {
    Name,
    Price,
};

class Product {
    private Name $name;
    private Price $price;
    /** @var Tag[] */
    private array $tags;

    public function method1(string $param1) {
    }
}
