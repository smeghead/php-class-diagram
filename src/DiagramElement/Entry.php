<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Options;

class Entry {
    public Options $options;
    public string $directory;
    public \stdClass $info;
    public function __construct(string $directory, \stdClass $info, Options $options) {
        $this->directory = $directory;
        $this->info = $info;
        $this->options = $options;
    }

    public function dump($level = 0): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        $meta = $this->info->type->meta === 'Stmt_Interface' ? 'interface' : 'class';
        if ($this->options->classProperties() || $this->options->classMethods()) {
            $lines[] = sprintf('%s%s %s {', $indent, $meta, $this->info->type->name);
            if ($this->options->classProperties()) {
                foreach ($this->info->properties as $p) {
                    $lines[] = sprintf('  %s%s : %s', $indent, $p->name, $p->type->name);
                }
            }
            if ($this->options->classMethods()) {
                foreach ($this->info->methods as $m) {
                    $params = array_map(function($x){
                        return $x->name;
                    }, $m->params);
                    $lines[] = sprintf('  %s%s(%s)', $indent, $m->name, implode(', ', $params));
                }
            }
            $lines[] = sprintf('%s}', $indent);
        } else {
            $lines[] = sprintf('%s%s %s', $indent, $meta, $this->info->type->name);
        }
        return $lines;
    }

    public function getArrows(): array {
        $arrows = [];
        //フィールド変数の型に対しての依存をArrowとして返却する。
        //FIXME コレクションへの依存は未検出
        foreach ($this->info->properties as $p) {
            $arrows[] = new Arrow($this->info->type->name, $p->type->name);
        }
        return $arrows;
    }
}
