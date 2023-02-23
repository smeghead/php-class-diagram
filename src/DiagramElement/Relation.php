<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Config\Options;

class Relation {
    private Options $options;
    private Package $package;

    /**
     * @param Entry[] $entries
     */
    public function __construct(array $entries, Options $options) {
        $this->options = $options;
        $this->package = new Package([], 'ROOT', $options);
        foreach ($entries as $e) {
            $this->package->addEntry(preg_split('/[\\\\\/]/', $e->getDirectory()), $e);
        }
    }

    public function getPackage(): Package {
        return $this->package;
    }

    public function dump(): array {
        $lines = ['@startuml class-diagram'];
        $lines = array_merge($lines, array_map(function($x){ return '  ' . $x;}, $this->options->headers()));
        $lines = array_merge($lines, $this->package->dump());
        $lines = array_merge($lines, $this->getRelations());
        $lines[] = '@enduml';

        return $lines;
    }

    public function getRelations(): array {
        $entities = $this->package->getEntries();
        $relation_expressions = array_map(function(Arrow $x) use ($entities){
            foreach ($entities as $e) {
                if ($e->getClass()->getClassType()->equals($x->getTo())) {
                    return $x->toString($e->getClass());
                }
            }
            return null;
        }, $this->package->getArrows());
        $relation_expressions = array_filter($relation_expressions);
        sort($relation_expressions);
        return array_unique($relation_expressions);
    }

    public function dumpPackages(): array {
        $lines = ['@startuml package-related-diagram'];
        $lines = array_merge($lines, $this->options->headers());
        $lines = array_merge($lines, $this->package->dumpPackages());
        $uses = $this->getUses();
        $packageRelations = new PackageRelations($uses, $this->package);
        $lines = array_merge($lines, $packageRelations->getArrows());
        $lines[] = '@enduml';

        return $lines;
    }

    public function getUses(): array {
        return $this->package->getUses([]);
    }
}
