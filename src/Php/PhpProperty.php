<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ {
    Property,
};

class PhpProperty {
    public string $name;
    public PhpType $type;
    public PhpAccessModifier $accessModifier;

    public function __construct(Property $p, PhpClass $class) {
        $this->name = $p->props[0]->name->toString();
        $this->type = $type = $class->findTypeByTypeParts($p, 'type', 'var');
        $this->accessModifier = new PhpAccessModifier($p);
    }
}
