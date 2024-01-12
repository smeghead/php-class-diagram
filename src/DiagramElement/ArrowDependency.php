<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Php\PhpClass;

final class ArrowDependency extends Arrow
{
    protected string $figure = '..>';

    public function toString(PhpClass $toClass): string
    {
        if (strpos($this->getTo()->getName(), '[]') !== false) {
            // ex. Product[]
            return $this->getExpression($toClass, false);
        }
        if (strpos($this->getTo()->getName(), 'array<') === 0) {
            // ex. array<Product> or array<int, Product>
            return $this->getExpression($toClass, false);
        }
        if (strpos($this->getTo()->getName(), 'non-empty-array<') === 0) {
            // ex. non-empty-array<Product> or non-empty-array<int, Product>
            return $this->getExpression($toClass, true);
        }
        return sprintf('  %s %s %s', $this->getFrom()->getClassNameAlias(), $this->figure, $toClass->getClassNameAlias());
    }

    private function getExpression(PhpClass $toClass, bool $nonEmpty): string
    {
        return sprintf(
            '  %s "1" %s "%s" %s',
            $this->getFrom()->getClassNameAlias(),
            $this->figure,
            $nonEmpty ? '1..*' : '*',
            str_replace('[]', '', $toClass->getClassNameAlias())
        );
    }
}
