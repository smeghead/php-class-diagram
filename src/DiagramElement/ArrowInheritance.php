<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Php\PhpClass;

class ArrowInheritance extends Arrow {
    protected string $figure = '<|--';

    public function toString(PhpClass $toClass): string {
        return sprintf('  %s %s %s', $toClass->getLogicalName(), $this->figure, $this->getFrom()->getLogicalName());
    }
}
