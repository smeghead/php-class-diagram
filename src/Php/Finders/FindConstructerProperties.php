<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php\Finders;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;

class FindConstructerProperties
{
    private array $properties = [];
    private ?ClassMethod $constructer;

    public function __construct(ClassLike $class)
    {
        $finder = new NodeFinder();
        /** @var ClassMethod $constructer */
        $constructer = $finder->findFirst($class, function (Node $node) {
            return $node instanceof ClassMethod && $node->name->toString() === '__construct';
        });
        if ($constructer === null) {
            return;
        }
        $this->constructer = $constructer;
        foreach ($constructer->getParams() as $p) {
            if ($p->flags & Class_::MODIFIER_PUBLIC) {
                $this->properties[] = $p;
            } else if ($p->flags & Class_::MODIFIER_PRIVATE) {
                $this->properties[] = $p;
            } else if ($p->flags & Class_::MODIFIER_PROTECTED) {
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
