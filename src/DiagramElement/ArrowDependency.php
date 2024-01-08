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
            return $this->getExpression($toClass);
        }
        if (strpos($this->getTo()->getName(), 'array<') === 0) {
            // ex. array<Product> or array<int, Product>
            return $this->getExpression($toClass);
        }
        return sprintf('  %s %s %s', $this->getFrom()->getClassNameAlias(), $this->figure, $toClass->getClassNameAlias());
    }

    private function getExpression(PhpClass $toClass): string
    {
        return sprintf('  %s "1" %s "*" %s', $this->getFrom()->getClassNameAlias(), $this->figure, str_replace('[]', '', $toClass->getClassNameAlias()));
    }
}
