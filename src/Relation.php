<?php
namespace Smeghead\PhpClassDiagram;

use Smeghead\PhpClassDiagram\ {
    Entry,
    Namespace_,
};

class Relation {
    private Namespace_ $namespace;

    public function __construct(array $entries) {
        $this->namespace = new Namespace_('ROOT');
        foreach ($entries as $e) {
            $this->namespace->addEntry(explode('/', $e->directory), $e);
        }
    }

    public function getNamespace(): Namespace_ {
        return $this->namespace;
    }

    public function dump(): array {
        $lines = ['@startuml'];
        $lines = array_merge($lines, $this->namespace->dump());
        $lines[] = '@enduml';

        return $lines;
    }
}
