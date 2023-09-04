<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php\Finders;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use Smeghead\PhpClassDiagram\Php\PhpType;

final class FindUsePhpTypes
{
    private ClassLike $class;

    public function __construct(ClassLike $class)
    {
        $this->class = $class;
    }

    /**
     * @return PhpType[] use class types.
     */
    public function collectTypes(): array
    {
        $finder = new NodeFinder();
        $methods = $finder->find($this->class, function (Node $node) {
            return $node instanceof ClassMethod;
        });
        /** @var FullyQualified[] */
        $useClasses = [];
        foreach ($methods as $m) {
            $useClasses = array_merge($useClasses, $finder->find($m, function (Node $node) {
                return $node instanceof FullyQualified;
            }));
        }
        // @phpstan-ignore-next-line
        return array_map(function(FullyQualified $x): PhpType {
            $parts = $x->parts;
            $name = array_pop($parts);
            return new PhpType($parts, '', $name);
        }, $useClasses);
    }
}
