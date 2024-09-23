<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Modifiers;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\{
    Class_,
    ClassConst,
    ClassMethod,
    Property,
};

final class PhpAccessModifier
{
    private bool $public = false;
    private bool $protected = false;
    private bool $private = false;
    private bool $abstract = false;
    private bool $final = false;
    private bool $static = false;

    public function __construct(ClassConst|Property|ClassMethod|Param $stmt)
    {
        if ($stmt instanceof Param) {
            $this->public = $this->parseFlagsPublic($stmt->flags);
            $this->protected = (bool)($stmt->flags & Modifiers::PROTECTED);
            $this->private = (bool)($stmt->flags & Modifiers::PRIVATE);
        } else {
            $this->public = $stmt->isPublic();
            $this->protected = $stmt->isProtected();
            $this->private = $stmt->isPrivate();
            if ($stmt instanceof ClassMethod) {
                $this->static = $stmt->isStatic();
                $this->abstract = $stmt->isAbstract();
            }
        }
    }

    private function parseFlagsPublic(int $flags): bool
    {
        if ($flags & Modifiers::PUBLIC) {
            return true;
        }
        if ($flags & Modifiers::PUBLIC_SET) {
            return true;
        }
        if ($flags & Modifiers::PROTECTED_SET) {
            return true;
        }
        if ($flags & Modifiers::PRIVATE_SET) {
            return true;
        }
        return false;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function isProtected(): bool
    {
        return $this->protected;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }
}
