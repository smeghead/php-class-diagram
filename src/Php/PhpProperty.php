<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ {
    Property,
};

class PhpProperty {
    protected string $name;
    protected PhpTypeExpression $type;
    protected PhpAccessModifier $accessModifier;

    public function __construct(Property $p, PhpClass $class) {
        $this->name = $p->props[0]->name->toString();
        $this->type = PhpTypeExpression::buildByVar($p, $class->getNamespace(), $class->getUses());
        $this->accessModifier = new PhpAccessModifier($p);
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): PhpTypeExpression {
        return $this->type;
    }

    public function getAccessModifier(): PhpAccessModifier {
        return $this->accessModifier;
    }
}
