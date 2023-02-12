<?php declare(strict_types=1);

use Smeghead\PhpClassDiagram\Php\ {
  PhpAccessModifier,
};

class PhpAccessModifierDummy extends PhpAccessModifier {
    // public bool $public = false;
    // public bool $protected = false;
    // public bool $private = false;
    // public bool $abstract = false;
    // public bool $final = false;
    // public bool $static = false;

    public function __construct(\stdClass $method) {
        $this->public = isset($method->public);
        $this->protected = isset($method->protected);
        $this->private = isset($method->private);
        $this->abstract = isset($method->abstract);
        $this->final = isset($method->final);
        $this->static = isset($method->static);
    }
}
