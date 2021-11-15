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
        $entities = $this->namespace->getEntries();
        // TODO クラスの同一性をクラス名のみで判定している。本当はnamespaceも含めて判定する必要がある。
//        $classNames = array_map(function($x){
//            return $x->class->getClassType()->name;
//        }, $entities);
//        // 解析対象に含まれているクラスで絞り込む
//        $arrows = array_filter($this->namespace->getArrows(), function($x) use ($classNames) {
//            return in_array(str_replace('[]', '', $x->to->name), $classNames);
//        });
        $relation_expressions = array_map(function($x) use ($entities){
            foreach ($entities as $e) {
                //if ($e->class->getClassType()->name == str_replace('[]', '', $x->to->name)) {
                if ($e->class->getClassType()->equals($x->to)) {
                    return $x->toString($e->class);
                }
            }
            return null;
        }, $this->namespace->getArrows());
        $relation_expressions = array_filter($relation_expressions);
        sort($relation_expressions);
        return $relation_expressions;
    }
}
