<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\DiagramElement\Arrow;

class ArrowDependency extends Arrow {
    protected string $figure = '..>';

    public function toString(): string {
        if (strpos($this->to, '[]') === false) {
            return sprintf('  %s %s %s', $this->from, $this->figure, $this->to);
        }
        return sprintf('  %s "1" %s "*" %s', $this->from, $this->figure, str_replace('[]', '', $this->to));
    }
}
