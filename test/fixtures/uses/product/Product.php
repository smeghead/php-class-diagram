<?php
namespace hoge\fuga\product;

use hoge\fuga\product\Name;
use hoge\fuga\product\ {
    Price,
};
use hoge\fuga\product\tags\Tag;

class Product {
    /** @var Name 名前 */
    private $name;
    
    /** @var Price 名前 */
    private $price;
    
    /** @var Tag[] 付与されたタグ一覧 */
    private array $tags;
}
