<?php
namespace hoge\fuga\product;

class Product {
    /** @var array<Tag> */
    private array $tags = [];

    /** @var non-empty-array<Tag> */
    private array $nonEmptytags;

    /**
     * @return array<Tag>
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
