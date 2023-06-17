<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter;

use Smeghead\PhpClassDiagram\Php\PhpMethod;

interface MethodFormatter {
    public function body(PhpMethod $method): string;
}