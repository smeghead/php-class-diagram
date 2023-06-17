<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML;

use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PackageFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Package;

class PlantUmlPackageFormatter implements PackageFormatter {
    public function head(Package $package): string {
        return sprintf('package %s as %s {', $package->name, $package->getLogicalName());
    }
    public function tail(): string {
        return '}';
    }
}