<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

class PhpMethodParameter {
    private string $name;
    private PhpType $type;

    public function __construct(string $name, PhpType $type) {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): PhpType {
        return $this->type;
    }
}
