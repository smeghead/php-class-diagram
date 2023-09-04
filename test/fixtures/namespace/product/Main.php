<?php
namespace hoge\fuga\product;

use hoge\fuga\product\ {
    Product,
};

class Main {
 
    public function __construct()
    {
        $product = new Product();
        $product->method1('test');
    }
}
