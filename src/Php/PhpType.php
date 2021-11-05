<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

class PhpType {
    public string $name;
    public array $namespace;

    public function __construct(array $namespace, string $name) {
        $this->namespace = $namespace;
        $this->name = $name;
    }
}
