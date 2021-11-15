<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use Smeghead\PhpClassDiagram\Options;
use Smeghead\PhpClassDiagram\DiagramElement\ {
    Namespace_,
};

class Relation {
    private Options $options;
    private Namespace_ $namespace;

    public function __construct(array $entries, Options $options) {
        $this->options = $options;
        Namespace_::init();
        $this->namespace = new Namespace_('ROOT', $options);
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
        $classNames = array_map(function($x){ return $x->class->getClassType()->name; }, $this->namespace->getEntries());
        $arrows = array_filter($this->namespace->getArrows(), function($x) use ($classNames) {
            return in_array(str_replace('[]', '', $x->to), $classNames);
        });
        $relation_expressions = array_map(function($x){
            return $x->toString();
        }, $arrows);
        sort($relation_expressions);
        return $relation_expressions;
    }
}
