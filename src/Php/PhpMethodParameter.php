<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

class PhpMethodParameter {
    public string $name;
    public PhpType $type;

    public function __construct(string $name, PhpType $type) {
        $this->name = $name;
        $this->type = $type;
    }
}
