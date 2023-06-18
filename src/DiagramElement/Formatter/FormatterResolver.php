<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid\MermaidDiagramFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid\MermaidEntryFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid\MermaidMethodFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid\MermaidPackageFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid\MermaidPropertyFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML\PlantUmlDiagramFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML\PlantUmlEntryFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML\PlantUmlMethodFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML\PlantUmlPackageFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML\PlantUmlPropertyFormatter;

class FormatterResolver {
    private Options $options;
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function getDiagramFormatter(): DiagramFormatter
    {
        if ($this->options->target() === Options::TARGET_MERMAID) {
            return new MermaidDiagramFormatter();
        }
        return new PlantUmlDiagramFormatter();
    }
    public function getPackageFormatter(): PackageFormatter
    {
        if ($this->options->target() === Options::TARGET_MERMAID) {
            return new MermaidPackageFormatter();
        }
        return new PlantUmlPackageFormatter();
    }
    public function getEntryFormatter(): EntryFormatter
    {
        if ($this->options->target() === Options::TARGET_MERMAID) {
            return new MermaidEntryFormatter();
        }
        return new PlantUmlEntryFormatter();
    }
    public function getPropertyFormatter(): PropertyFormatter
    {
        if ($this->options->target() === Options::TARGET_MERMAID) {
            return new MermaidPropertyFormatter();
        }
        return new PlantUmlPropertyFormatter();
    }
    public function getMethodFormatter(): MethodFormatter
    {
        if ($this->options->target() === Options::TARGET_MERMAID) {
            return new MermaidMethodFormatter();
        }
        return new PlantUmlMethodFormatter();
    }
}