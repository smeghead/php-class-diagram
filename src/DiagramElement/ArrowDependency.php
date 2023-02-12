<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Php\PhpClass;

class ArrowDependency extends Arrow {
    protected string $figure = '..>';

    public function toString(PhpClass $toClass): string {
        if (strpos($this->getTo()->getName(), '[]') === false) {
            return sprintf('  %s %s %s', $this->getFrom()->getLogicalName(), $this->figure, $toClass->getLogicalName());
        }
        return sprintf('  %s "1" %s "*" %s', $this->getFrom()->getLogicalName(), $this->figure, str_replace('[]', '', $toClass->getLogicalName()));
    }
}
