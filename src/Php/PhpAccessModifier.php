<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ {
    ClassMethod,
};

class PhpAccessModifier {
    protected bool $public = false;
    protected bool $protected = false;
    protected bool $private = false;
    protected bool $abstract = false;
    protected bool $final = false;
    protected bool $static = false;

    public function __construct(Stmt $stmt) {
        $this->public = $stmt->isPublic();
        $this->protected = $stmt->isProtected();
        $this->private = $stmt->isPrivate();
        $this->static = $stmt->isStatic();
        if ($stmt instanceOf ClassMethod) {
            $this->abstract = $stmt->isAbstract();
        }
    }

    public function isPublic(): bool {
        return $this->public;
    }

    public function isProtected(): bool {
        return $this->protected;
    }

    public function isPrivate(): bool {
        return $this->private;
    }

    public function isAbstract(): bool {
        return $this->abstract;
    }

    public function isFinal(): bool {
        return $this->final;
    }

    public function isStatic(): bool {
        return $this->static;
    }
}
