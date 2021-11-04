<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use Smeghead\PhpClassDiagram\ {
    Namespace_,
    Entry,
    Arrow,
};

class Relation {
    private Namespace_ $namespace;

    public function __construct(array $entries) {
        Namespace_::init();
        $this->namespace = new Namespace_('ROOT');
        foreach ($entries as $e) {
            $this->namespace->addEntry(preg_split('/[\\\\\/]/', $e->directory), $e);
        }
    }

    public function getNamespace(): Namespace_ {
        return $this->namespace;
    }

    public function dump(): array {
        $lines = ['@startuml'];
        $lines = array_merge($lines, $this->namespace->dump());
        $lines = array_merge($lines, $this->getRelations());
        $lines[] = '@enduml';

        return $lines;
    }

    public function getRelations(): array {
        $classNames = array_map(function($x){ return $x->info->name; }, $this->namespace->getEntries());
        $arrows = array_filter($this->namespace->getArrows(), function($x) use ($classNames) {
            return in_array($x->to, $classNames);
        });
        $relation_expressions = array_map(function($x){
            return sprintf('  %s ..> %s', $x->from, $x->to);
        }, $arrows);
        sort($relation_expressions);
        return $relation_expressions;
    }
}
