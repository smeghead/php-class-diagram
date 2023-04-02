<?php
namespace hoge\fuga\product;

use external\sales;

class Exception extends \Exception {
    private \Exception $inner;
    
    public function getInner(\Exception $e): \Exception {
    }
    public function external(): \external\Exception {
    }
}
