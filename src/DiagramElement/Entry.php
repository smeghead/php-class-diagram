<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Options;
use Smeghead\PhpClassDiagram\Php\ {
    PhpClass,
    PhpAccessModifier,
};

class Entry {
    public Options $options;
    public string $directory;
    public PhpClass $class;
    public function __construct(string $directory, PhpClass $class, Options $options) {
        $this->directory = $directory;
        $this->class = $class;
        $this->options = $options;
    }

    public function dump($level = 0): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        $meta = $this->class->getClassType()->meta === 'Stmt_Interface' ? 'interface' : 'class';
        if ($this->options->classProperties() || $this->options->classMethods()) {
            $lines[] = sprintf('%s%s %s {', $indent, $meta, $this->class->getLogicalName());
            if ($this->options->classProperties()) {
                foreach ($this->class->getProperties() as $p) {
                    $lines[] = sprintf('  %s%s%s : %s', $indent, $this->modifier($p->accessModifier), $p->name, $p->type->name);
                }
            }
            if ($this->options->classMethods()) {
                foreach ($this->class->getMethods() as $m) {
                    $params = array_map(function($x){
                        return $x->name;
                    }, $m->params);
                    $lines[] = sprintf('  %s%s%s(%s)', $indent, $this->modifier($m->accessModifier), $m->name, implode(', ', $params));
                }
            }
            $lines[] = sprintf('%s}', $indent);
        } else {
            $lines[] = sprintf('%s%s %s', $indent, $meta, $this->class->getLogicalName());
        }
        return $lines;
    }

    private function modifier(PhpAccessModifier $modifier): string {
        $expressions = [];
        if ($modifier->static) {
            $expressions[] = '{static}';
        }
        if ($modifier->abstract) {
            $expressions[] = '{abstract}';
        }
        if ($modifier->public) {
            $expressions[] = '+';
        }
        if ($modifier->protected) {
            $expressions[] = '#';
        }
        if ($modifier->private) {
            $expressions[] = '-';
        }
        return implode(' ' , $expressions);
    }

    public function getArrows(): array {
        $arrows = [];
        //フィールド変数の型に対しての依存をArrowとして追加する。
        foreach ($this->class->getProperties() as $p) {
            $arrows[] = new ArrowDependency($this->class, $p->type);
        }
        foreach ($this->class->getMethods() as $m) {
            if ( ! $m->accessModifier->public) {
                continue;
            }
            if (count($m->params) > 0) {
                continue;
            }
            $arrows[] = new ArrowDependency($this->class, $m->type);
        }
        $extends = $this->class->getExtends();
        if ( ! empty($extends)) {
            //継承先に対してArrowを追加する。
            foreach ($extends as $extend) {
                $arrows[] = new ArrowInheritance($this->class, $extend);
            }
        }

        return $arrows;
    }
}
