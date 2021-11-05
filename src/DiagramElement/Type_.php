<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

class Type_ {
    public array $namespace;
    public string $name;

    public function __construct(array $namespace, string $name) {
        $this->namespace = $namespace;
        $this->name = $name;
    }
}
