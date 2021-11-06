<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

class Entry {
    public string $directory;
    public \stdClass $info;
    public function __construct(string $directory, \stdClass $info) {
        $this->directory = $directory;
        $this->info = $info;
    }

    public function dump($level = 0): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        $meta = $this->info->type->meta === 'Stmt_Interface' ? 'interface' : 'class';
        $lines[] = sprintf('%s%s %s', $indent, $meta, $this->info->type->name);
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
