<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter;

interface DiagramFormatter {
    public function head(): string;
    public function tail(): string;
}