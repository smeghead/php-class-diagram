<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ {
    ClassMethod,
};

class PhpAccessModifier {
    public bool $public = false;
    public bool $protected = false;
    public bool $private = false;
    public bool $abstract = false;
    public bool $final = false;
    public bool $static = false;

    public function __construct(Stmt $stmt) {
        $this->public = $stmt->isPublic();
        $this->protected = $stmt->isProtected();
        $this->private = $stmt->isPrivate();
        $this->static = $stmt->isStatic();
        if ($stmt instanceOf ClassMethod) {
            $this->abstract = $stmt->isAbstract();
        }
    }
}
