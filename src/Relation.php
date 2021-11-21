<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use Smeghead\PhpClassDiagram\DiagramElement\Package;

class Relation {
    private Options $options;
    private Package $namespace;

    public function __construct(array $entries, Options $options) {
        $this->options = $options;
        $this->namespace = new Package([], 'ROOT', $options);
        foreach ($entries as $e) {
            $this->namespace->addEntry(preg_split('/[\\\\\/]/', $e->directory), $e);
        }
    }

    public function getNamespace(): Package {
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
        $relation_expressions = array_map(function($x) use ($entities){
            foreach ($entities as $e) {
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

    public function dumpPackages(): array {
        $lines = ['@startuml'];
        $lines = array_merge($lines, $this->namespace->dumpPackages());
        $uses = $this->getUses();
        $targetNamespaces = $this->namespace->getTargetNamespaces();
        $all = [];
        $packageRelations = [];
        foreach ($uses as $namespace => $us) {
            $namespaces = array_unique(array_map(function($x){
                return implode('.', $x->namespace);
            }, $us));
            // 対象となっているnamespace以外のnamespaceは、即席で定義する必要がある。
            $all = array_unique(array_merge($all, $namespaces));
            $packageRelations[$namespace] = array_map(function($x) use ($targetNamespaces){
                return $this->displayNamespace($x, $targetNamespaces);
            }, $namespaces);
        }
        foreach (array_diff($all, array_keys($targetNamespaces)) as $external) {
            $lines[] = sprintf('  package %s', $external); 
        }
        foreach ($packageRelations as $namespace => $dependencies) {
            $namespace = $this->displayNamespace($namespace, $targetNamespaces);
            foreach ($dependencies as $d) {
                if (empty($d)) {
                    continue;
                }
                $lines[] = sprintf('  %s --> %s', $namespace, $d);
            }
        }
        $lines[] = '@enduml';

        return $lines;
    }
    private function displayNamespace($namespace, $targetNamespaces) {
        if (in_array($namespace, array_keys($targetNamespaces))) {
            return $targetNamespaces[$namespace]; // 解析対象のnamespaceはディレクトリ名で表示
        } else {
            return $namespace; //外部のnamespaceはnamespace表示
        }
    }

    public function getUses(): array {
        return $this->namespace->getUses([]);
    }
}
