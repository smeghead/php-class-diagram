<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter;

use Smeghead\PhpClassDiagram\Php\PhpProperty;

interface PropertyFormatter {
    public function body(PhpProperty $property): string;
}