<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\Entry;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\EntryFormatter;

class MermaidEntryFormatter implements EntryFormatter {
    public function head(Options $options, Entry $entry, bool $withBlock): string {
        $class = $entry->getClass();
        $meta = $class->getClassType()->getMetaName();
        $classSummary = ($options->classNameSummary()
            ? $class->getDescription()
            : '');
        $classIdentifier = sprintf(
            '%s %s["%s"]',
            $meta,
            $class->getClassNameAlias(),
            $class->getClassType()->getName() . (empty($classSummary) ? '' : sprintf("\\n<b>%s</b>", $classSummary))
        );
        return sprintf('%s%s', $classIdentifier, $withBlock ? ' {' : '');
    }
    public function tail(): string {
        return '}';
    }
}