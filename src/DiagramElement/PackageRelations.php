<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

class PackageRelations {
    /** @var Smeghead\PhpClassDiagram\Php\PhpType[] */
    private array $uses;
    /** @var Smeghead\PhpClassDiagram\Php\PhpType[] */
    private array $targetPackages;

    public function __construct(array $uses, array $targetPackages) {
        $this->uses = $uses;
        $this->targetPackages = $targetPackages;
    }

    private function displayPackage($package) {
        if (in_array($package, array_keys($this->targetPackages))) {
            return $this->targetPackages[$package]; // 解析対象のpackageはディレクトリ名で表示
        } else {
            return $package; //外部のpackageはpackage表示
        }
    }
    
    public function getArrows(): array {
        $lines = [];
        $all = [];
        $packageRelations = [];
        foreach ($this->uses as $namespace => $us) {
            $packages = array_unique(array_map(function($x){
                return implode('.', $x->namespace);
            }, $us));
            // 対象となっているpackage以外のpackageは、即席で定義する必要がある。
            $all = array_unique(array_merge($all, $packages));
            $packageRelations[$namespace] = array_map(function($x) {
                return $this->displayPackage($x, $this->targetPackages);
            }, $packages);
        }
        foreach (array_diff($all, array_keys($this->targetPackages)) as $external) {
            $lines[] = sprintf('  package %s', $external); 
        }
        $arrows = [];
        foreach ($packageRelations as $package => $dependencies) {
            $package = $this->displayPackage($package, $this->targetPackages);
            foreach ($dependencies as $d) {
                if (empty($d)) {
                    continue;
                }
                $arrows[] = new PackageArrow($package, $d);
            }
        }
        $arrows = $this->mergeBothSideArrow($arrows);
        foreach ($arrows as $arrow) {
            $lines[] = $arrow->toString();
        }
        return $lines;
    }

    private function mergeBothSideArrow(array $arrows): array {
        $merged = [];
        $bothSideArrows = [];
        foreach ($arrows as $a) {
            if ($this->existsUpsidedown($merged, $a)) {
                $bothSideArrows[] = $a;
            } else {
                $merged[] = $a;
            }
        }
        foreach ($bothSideArrows as $b) {
            foreach ($merged as &$m) {
                if ( ! $m->isOpposite($b)) {
                    continue;
                }
                $m->bothSideArrow();
                break;
            } unset($m);
        }
        return $merged;
    }

    private function existsUpsidedown(array $arrows, PackageArrow $arrow): bool {
        foreach ($arrows as $a) {
            if ($a->isOpposite($arrow)) {
                return true;
            }
        }
        return false;
    }
}
