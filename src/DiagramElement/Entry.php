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
    public PhpClass $info;
    public function __construct(string $directory, PhpClass $info, Options $options) {
        $this->directory = $directory;
        $this->info = $info;
        $this->options = $options;
    }

    public function dump($level = 0): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        $meta = $this->info->getClassType()->meta === 'Stmt_Interface' ? 'interface' : 'class';
        if ($this->options->classProperties() || $this->options->classMethods()) {
            $lines[] = sprintf('%s%s %s {', $indent, $meta, $this->info->getClassType()->name);
            if ($this->options->classProperties()) {
                foreach ($this->info->getProperties() as $p) {
                    $lines[] = sprintf('  %s%s%s : %s', $indent, $this->modifier($p->accessModifier), $p->name, $p->type->name);
                }
            }
            if ($this->options->classMethods()) {
                foreach ($this->info->getMethods() as $m) {
                    $params = array_map(function($x){
                        return $x->name;
                    }, $m->params);
                    $lines[] = sprintf('  %s%s%s(%s)', $indent, $this->modifier($m->accessModifier), $m->name, implode(', ', $params));
                }
            }
            $lines[] = sprintf('%s}', $indent);
        } else {
            $lines[] = sprintf('%s%s %s', $indent, $meta, $this->info->getClassType()->name);
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
        foreach ($this->info->getProperties() as $p) {
            $arrows[] = new ArrowDependency($this->info->getClassType()->name, $p->type->name);
        }
        $extends = $this->info->getExtends();
        if ( ! empty($extends)) {
            //継承先に対してArrowを追加する。
            foreach ($extends as $extend) {
                $arrows[] = new ArrowInheritance($this->info->getClassType()->name, $extend->name);
            }
        }

        return $arrows;
    }
}
