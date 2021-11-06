<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

class PhpType {
    public string $name;
    public string $meta;
    public array $namespace;

    public function __construct(array $namespace, string $meta, string $name) {
        $this->namespace = $namespace;
        $this->meta = $meta;
        $this->name = $name;
    }
}
