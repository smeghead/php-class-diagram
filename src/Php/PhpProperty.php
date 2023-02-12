<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ {
    Property,
};

class PhpProperty {
    protected string $name;
    protected PhpType $type;
    protected PhpAccessModifier $accessModifier;

    public function __construct(Property $p, PhpClass $class) {
        $this->name = $p->props[0]->name->toString();
        $this->type = $class->findTypeByTypeParts($p, 'type', 'var');
        $this->accessModifier = new PhpAccessModifier($p);
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): PhpType {
        return $this->type;
    }

    public function getAccessModifier(): PhpAccessModifier {
        return $this->accessModifier;
    }
}
