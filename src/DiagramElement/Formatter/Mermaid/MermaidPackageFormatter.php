<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid;

use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PackageFormatter;
use Smeghead\PhpClassDiagram\DiagramElement\Package;

class MermaidPackageFormatter implements PackageFormatter {
    public function head(Package $package): string {
        return sprintf('namespace %s {', $package->name);
    }
    public function tail(): string {
        return '}';
    }
}