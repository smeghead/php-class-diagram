<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpType;

class PhpProperty {
    public string $name;
    public PhpType $type;

    public function __construct(array $name, PhpType $type) {
        $this->name = $name;
        $this->type = $type;
    }
}
