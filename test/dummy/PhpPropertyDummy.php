<?php declare(strict_types=1);

use PhpParser\Node\Stmt\ {
    Property,
};

use Smeghead\PhpClassDiagram\Php\ {
    PhpType,
    PhpProperty,
    PhpAccessModifier,
};

class PhpPropertyDummy extends PhpProperty {
    public string $name;
    public PhpType $type;
    public PhpAccessModifier $accessModifier;

    public function __construct(string $name, PhpType $type, \stdClass $modifier) {
        $this->name = $name;
        $this->type = $type;
        $this->accessModifier = new PhpAccessModifierDummy($modifier);
    }
}
