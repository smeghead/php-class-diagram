<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid;

use Smeghead\PhpClassDiagram\DiagramElement\Formatter\DiagramFormatter;

class MermaidDiagramFormatter implements DiagramFormatter {
    public function head(): string {
        return 'classDiagram';
    }
    public function tail(): string {
        return '';
    }
}