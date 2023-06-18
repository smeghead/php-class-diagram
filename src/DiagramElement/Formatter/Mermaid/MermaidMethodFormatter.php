<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Formatter\Mermaid;

use Smeghead\PhpClassDiagram\DiagramElement\Formatter\MethodFormatter;
use Smeghead\PhpClassDiagram\Php\PhpAccessModifier;
use Smeghead\PhpClassDiagram\Php\PhpMethod;
use Smeghead\PhpClassDiagram\Php\PhpMethodParameter;

class MermaidMethodFormatter implements MethodFormatter {
    public function body(PhpMethod $method): string {
        $params = array_map(function (PhpMethodParameter $x) {
            return $x->getName();
        }, $method->getParams());
        return sprintf('%s%s(%s)', $this->modifier($method->getAccessModifier()), $method->getName(), implode(', ', $params));
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