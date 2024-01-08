<?php
namespace hoge\fuga\product;

class Product {
    /** @var array<Tag> */
    private array $tags = [];

    /**
     * @return array<Tag>
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
