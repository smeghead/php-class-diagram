<?php declare(strict_types=1);

use Smeghead\PhpClassDiagram\Php\ {
  PhpAccessModifier,
};

class PhpAccessModifierDummy extends PhpAccessModifier {
    public bool $public;
    public bool $protected;
    public bool $private;
    public bool $abstract;
    public bool $final;
    public bool $static;

    public function __construct(\stdClass $method) {
        $this->public = isset($method->public);
        $this->protected = isset($method->protected);
        $this->private = isset($method->private);
        $this->abstract = isset($method->abstract);
        $this->final = isset($method->final);
        $this->static = isset($method->static);
    }
}
