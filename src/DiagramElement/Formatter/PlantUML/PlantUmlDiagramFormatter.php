<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML;

use Smeghead\PhpClassDiagram\DiagramElement\Formatter\DiagramFormatter;

class PlantUmlDiagramFormatter implements DiagramFormatter {
    public function head(): string {
        return '@startuml class-diagram';
    }
    public function tail(): string {
        return '@enduml';
    }
}