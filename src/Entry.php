<?php
namespace Smeghead\PhpClassDiagram;

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
        $lines[] = sprintf('%sclass %s', $indent, $this->info->name);
        return $lines;
    }

}
