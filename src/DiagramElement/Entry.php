<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Generator;
use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\Division\DivisionColor;
use Smeghead\PhpClassDiagram\DiagramElement\Formatter\FormatterResolver;
use Smeghead\PhpClassDiagram\Php\{
    PhpClass,
    PhpAccessModifier,
    PhpEnumCase,
    PhpMethodParameter,
};

class Entry
{
    private Options $options;
    private string $directory;
    private PhpClass $class;

    public function __construct(string $directory, PhpClass $class, Options $options)
    {
        $this->directory = $directory;
        $this->class = $class;
        $this->options = $options;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getClass(): PhpClass
    {
        return $this->class;
    }

    public function dump($level = 0): array
    {
        $indent = str_repeat('  ', $level);
        $lines = [];

        $formatterResolver = new FormatterResolver($this->options);
        $entryFormatter = $formatterResolver->getEntryFormatter();
        $propertyFormatter = $formatterResolver->getPropertyFormatter();
        $methodFormatter = $formatterResolver->getMethodFormatter();
        if ($this->options->classProperties() || $this->options->classMethods()) {
            $lines[] = sprintf('%s%s', $indent, $entryFormatter->head($this->options, $this, true));
            if ($this->options->classProperties()) {
                foreach ($this->class->getProperties() as $p) {
                    $lines[] = sprintf('  %s%s', $indent, $propertyFormatter->body($p));
                }
            }
            if ($this->options->classMethods()) {
                foreach ($this->class->getMethods() as $m) {
                    $lines[] = sprintf('  %s%s', $indent, $methodFormatter->body($m));
                }
            }
            $lines[] = sprintf('%s%s', $indent, $entryFormatter->tail());
        } else {
            $lines[] = sprintf('%s%s', $indent, $entryFormatter->head($this->options, $this, false));
        }
        return $lines;
    }

    public function dumpDivisions($level = 0): array
    {
        $indent = str_repeat('  ', $level);
        $lines = [];
        $meta = $this->class->getClassType()->getMetaName();
        if ($meta === 'enum') {
            $lines[] = sprintf('%scard %s %s [', $indent, $this->class->getClassType()->getName(), DivisionColor::nextColor());
            $lines[] = sprintf('%s  %s', $indent, $this->class->getClassType()->getName());
            $description = $this->class->getDescription();
            if (!empty($description)) {
                $lines[] = sprintf('%s  <b>%s</b>', $indent, $description);
            }
            $lines[] = sprintf('%s  ====', $indent);
            $cases = $this->class->getEnumCases();
            $lines[] = implode(sprintf("\r\n%s  ----\r\n", $indent), array_map(function (PhpEnumCase $x) use ($indent) {
                $doc = $x->getDocString();
                if (empty($doc)) {
                    return sprintf('%s  %s', $indent, $x->getName());
                }
                return sprintf("%s  %s\r\n%s  <b>%s</b>", $indent, $x->getName(), $indent, $doc);
            }, $cases));
            $lines[] = sprintf('%s]', $indent);
        }
        return $lines;
    }

    public function getArrows(): array
    {
        $arrows = [];
        //フィールド変数の型に対しての依存をArrowとして追加する。
        foreach ($this->class->getProperties() as $p) {
            foreach ($p->getType()->getTypes() as $t) {
                $arrows[] = new ArrowDependency($this->class, $t);
            }
        }
        foreach ($this->class->getMethods() as $m) {
            if (!$m->getAccessModifier()->isPublic()) {
                continue;
            }
            foreach ($m->getParams() as $p) {
                foreach ($p->getType()->getTypes() as $t) {
                    $arrows[] = new ArrowDependency($this->class, $t);
                }
            }
            foreach ($m->getType()->getTypes() as $t) {
                $arrows[] = new ArrowDependency($this->class, $t);
            }
        }
        $extends = $this->class->getExtends();
        if (!empty($extends)) {
            //継承先に対してArrowを追加する。
            foreach ($extends as $extend) {
                $arrows[] = new ArrowInheritance($this->class, $extend);
            }
        }

        return $arrows;
    }
}
