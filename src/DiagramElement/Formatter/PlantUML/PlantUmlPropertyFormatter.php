<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter\PlantUML;

use Smeghead\PhpClassDiagram\DiagramElement\Formatter\PropertyFormatter;
use Smeghead\PhpClassDiagram\Php\PhpAccessModifier;
use Smeghead\PhpClassDiagram\Php\PhpProperty;

class PlantUmlPropertyFormatter implements PropertyFormatter {
    public function body(PhpProperty $property): string {
        return sprintf('%s%s : %s', $this->modifier($property->getAccessModifier()), $property->getName(), $property->getType()->getName());
    }

    private function modifier(PhpAccessModifier $modifier): string
    {
        $expressions = [];
        if ($modifier->isStatic()) {
            $expressions[] = '{static}';
        }
        if ($modifier->isAbstract()) {
            $expressions[] = '{abstract}';
        }
        if ($modifier->isPublic()) {
            $expressions[] = '+';
        }
        if ($modifier->isProtected()) {
            $expressions[] = '#';
        }
        if ($modifier->isPrivate()) {
            $expressions[] = '-';
        }
        return implode(' ', $expressions);
    }
}