<?php
namespace Smeghead\PhpClassDiagram;

class Namespace_ {
    public string $name;
    public array $children = [];
    public array $entries = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function addEntry(array $paths, Entry $entry): void {
        if (count($paths) === 0) {
            $this->entries[] = $entry;
            return;
        }
        $dir = array_shift($paths);
        $ns = $this->findChild($dir);
        $ns->addEntry($paths, $entry);
        return;
    }

    private function findChild(string $dir): Namespace_ {
        foreach ($this->children as $c) {
            if ($c->name === $dir) {
                return $c;
            }
        }
        // if not exists, generate Namespace_.
        $this->children[] = new self($dir);
        return end($this->children);
    }

    public function dump($level = 0): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf('%spackage "%s" <<Rectangle>> {', $indent, $this->name);
        }
        foreach ($this->children as $n) {
            $lines = array_merge($lines, $n->dump($level));
        }
        foreach ($this->entries as $e) {
            $lines = array_merge($lines, $e->dump($level + 1));
        }
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf('%s}', $indent);
        }
        return $lines;
    }
}
