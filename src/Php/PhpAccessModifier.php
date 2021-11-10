<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ {
    ClassMethod,
};

class PhpAccessModifier {
    public bool $public;
    public bool $protected;
    public bool $private;
    public bool $abstract;
    public bool $final;
    public bool $static;

    public function __construct(ClassMethod $method) {
        $this->public = $method->isPublic();
        $this->protected = $method->isProtected();
        $this->private = $method->isPrivate();
        $this->abstract = $method->isAbstract();
        $this->final = $method->isFinal();
        $this->static = $method->isStatic();
    }
}
