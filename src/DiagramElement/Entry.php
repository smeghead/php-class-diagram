<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Generator;
use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\Division\DivisionColor;
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
        // $meta = $this->class->getClassType()->getMeta() === 'Stmt_Interface' ? 'interface' : 'class';
        $meta = $this->class->getClassType()->getMetaName();
        if ($this->options->classProperties() || $this->options->classMethods()) {
            $lines[] = sprintf('%s%s "%s" as %s {', $indent, $meta, $this->class->getClassType()->getName(), $this->class->getClassNameAlias());
            if ($this->options->classProperties()) {
                foreach ($this->class->getProperties() as $p) {
                    $lines[] = sprintf('  %s%s%s : %s', $indent, $this->modifier($p->getAccessModifier()), $p->getName(), $p->getType()->getName());
                }
            }
            if ($this->options->classMethods()) {
                foreach ($this->class->getMethods() as $m) {
                    $params = array_map(function (PhpMethodParameter $x) {
                        return $x->getName();
                    }, $m->getParams());
                    $lines[] = sprintf('  %s%s%s(%s)', $indent, $this->modifier($m->getAccessModifier()), $m->getName(), implode(', ', $params));
                }
            }
            $lines[] = sprintf('%s}', $indent);
        } else {
            $lines[] = sprintf('%s%s "%s" as %s', $indent, $meta, $this->class->getClassType()->getName(), $this->class->getClassNameAlias());
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
            $lines[] = sprintf('%s  ====', $indent);
            $cases = $this->class->getEnumCases();
            $lines[] = implode(sprintf("\r\n%s  ----\r\n", $indent), array_map(function (PhpEnumCase $x) use($indent) {
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
