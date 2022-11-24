<?php
namespace hoge\fuga\product;

use hoge\fuga\product\attribute\ {
    Name,
    Price,
};
use hoge\fuga\product\config\Config;

class Product {
    private Name $name;
    private Price $price;
    private Config $config;

    /** @return Product product */
    public function method1(string $param1) {
    }
}
