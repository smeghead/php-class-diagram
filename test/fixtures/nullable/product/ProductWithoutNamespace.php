<?php
use hoge\fuga\product\Name;
use hoge\fuga\product\ {
    Price,
    Tag,
};

class ProductWithoutNamespace {
    /** @var Name 名前 */
    private $name;
    
    /** @var Price 名前 */
    private $price;
    
    /** @var Tag[] 付与されたタグ一覧 */
    private array $tags;

    public function nullable(?Name $name): ?Name {
        return $name;
    }
}
