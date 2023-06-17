<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\Entry;

interface EntryFormatter {
    public function head(Options $options, Entry $entry, bool $withBlock): string;
    public function tail(): string;
}