<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php\Finders;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;

final class FindConstructerProperties
{
    /** @var \PhpParser\Node\Param[] */
    private array $properties = [];
    private ?ClassMethod $constructer;

    public function __construct(ClassLike $class)
    {
        $finder = new NodeFinder();
        /** @var ClassMethod|null $constructer */
        $constructer = $finder->findFirst($class, function (Node $node) {
            return $node instanceof ClassMethod && $node->name->toString() === '__construct';
        });
        if ($constructer === null) {
            return;
        }
        $this->constructer = $constructer;
        foreach ($constructer->getParams() as $p) {
            if ($p->flags & Modifiers::PUBLIC) {
                $this->properties[] = $p;
            } else if ($p->flags & Modifiers::PRIVATE) {
                $this->properties[] = $p;
            } else if ($p->flags & Modifiers::PROTECTED) {
                $this->properties[] = $p;
            } else if ($p->flags & Modifiers::PUBLIC_SET) {
                $this->properties[] = $p;
            } else if ($p->flags & Modifiers::PROTECTED_SET) {
                $this->properties[] = $p;
            } else if ($p->flags & Modifiers::PRIVATE_SET) {
                $this->properties[] = $p;
            }
        }
    }

    /**
     * @return Param[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getConstructer(): ?ClassMethod
    {
        return $this->constructer;
    }
}
