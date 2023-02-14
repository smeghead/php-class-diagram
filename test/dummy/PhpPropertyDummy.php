<?php declare(strict_types=1);

use PhpParser\Node\Stmt\ {
    Property,
};

use Smeghead\PhpClassDiagram\Php\ {
    PhpType,
    PhpProperty,
    PhpAccessModifier,
    PhpTypeExpression,
};

class PhpPropertyDummy extends PhpProperty {
    // public string $name;
    // public PhpType $type;
    // public PhpAccessModifier $accessModifier;

    public function __construct(string $name, PhpTypeExpression $type, \stdClass $modifier) {
        $this->name = $name;
        $this->type = $type;
        $this->accessModifier = new PhpAccessModifierDummy($modifier);
    }
}
