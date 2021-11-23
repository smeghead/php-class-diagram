<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Config\Options;

class Relation {
    private Options $options;
    private Package $package;

    public function __construct(array $entries, Options $options) {
        $this->options = $options;
        $this->package = new Package([], 'ROOT', $options);
        foreach ($entries as $e) {
            $this->package->addEntry(preg_split('/[\\\\\/]/', $e->directory), $e);
        }
    }

    public function getPackage(): Package {
        return $this->package;
    }

    public function dump(): array {
        $lines = ['@startuml class-diagram'];
        $lines = array_merge($lines, $this->package->dump());
        $lines = array_merge($lines, $this->getRelations());
        $lines[] = '@enduml';

        return $lines;
    }

    public function getRelations(): array {
        $entities = $this->package->getEntries();
        $relation_expressions = array_map(function($x) use ($entities){
            foreach ($entities as $e) {
                if ($e->class->getClassType()->equals($x->to)) {
                    return $x->toString($e->class);
                }
            }
            return null;
        }, $this->package->getArrows());
        $relation_expressions = array_filter($relation_expressions);
        sort($relation_expressions);
        return $relation_expressions;
    }

    public function dumpPackages(): array {
        $lines = ['@startuml package-related-diagram'];
        $lines = array_merge($lines, $this->package->dumpPackages());
        $uses = $this->getUses();
        $targetPackages = $this->package->getTargetPackages();
        $all = [];
        $packageRelations = [];
        foreach ($uses as $namespace => $us) {
            $packages = array_unique(array_map(function($x){
                return implode('.', $x->namespace);
            }, $us));
            // 対象となっているpackage以外のpackageは、即席で定義する必要がある。
            $all = array_unique(array_merge($all, $packages));
            $packageRelations[$namespace] = array_map(function($x) use ($targetPackages){
                return $this->displayPackage($x, $targetPackages);
            }, $packages);
        }
        foreach (array_diff($all, array_keys($targetPackages)) as $external) {
            $lines[] = sprintf('  package %s', $external); 
        }
        foreach ($packageRelations as $package => $dependencies) {
            $package = $this->displayPackage($package, $targetPackages);
            foreach ($dependencies as $d) {
                if (empty($d)) {
                    continue;
                }
                $lines[] = sprintf('  %s --> %s', $package, $d);
            }
        }
        $lines[] = '@enduml';

        return $lines;
    }
    private function displayPackage($package, $targetPackages) {
        if (in_array($package, array_keys($targetPackages))) {
            return $targetPackages[$package]; // 解析対象のpackageはディレクトリ名で表示
        } else {
            return $package; //外部のpackageはpackage表示
        }
    }

    public function getUses(): array {
        return $this->package->getUses([]);
    }
}
