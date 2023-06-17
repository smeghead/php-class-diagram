<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter;

use Smeghead\PhpClassDiagram\Config\Options;
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
        return new PlantUmlDiagramFormatter();
    }
    public function getPackageFormatter(): PackageFormatter
    {
        return new PlantUmlPackageFormatter();
    }
    public function getEntryFormatter(): EntryFormatter
    {
        return new PlantUmlEntryFormatter();
    }
    public function getPropertyFormatter(): PropertyFormatter
    {
        return new PlantUmlPropertyFormatter();
    }
    public function getMethodFormatter(): MethodFormatter
    {
        return new PlantUmlMethodFormatter();
    }
}