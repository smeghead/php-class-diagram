<?php
namespace Smeghead\PhpClassDiagram;

class Entry {
    public string $directory;
    public \stdClass $info;
    public function __construct(string $directory, \stdClass $info) {
        $this->directory = $directory;
        $this->info = $info;
    }
}
