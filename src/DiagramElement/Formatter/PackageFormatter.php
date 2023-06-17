<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter;

use Smeghead\PhpClassDiagram\DiagramElement\Package;

interface PackageFormatter {
    public function head(Package $package): string;
    public function tail(): string;
}