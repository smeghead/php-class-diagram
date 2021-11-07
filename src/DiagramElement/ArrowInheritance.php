<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\DiagramElement\Arrow;

class ArrowInheritance extends Arrow {
    protected string $figure = '<|--';

    public function toString(): string {
        return sprintf('  %s %s %s', $this->to, $this->figure, $this->from);
    }
}
