<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\DiagramElement\ExternalPackage\PackageHierarchy;
use Smeghead\PhpClassDiagram\Php\PhpType;

final class PackageRelations
{
    /** @var array<string, array<int, PhpType>> */
    private array $uses;
    private Package $rootPackage;

    /**
     * @param array<string, array<int, PhpType>> $uses
     * @param Package $rootPackage
     */
    public function __construct(array $uses, Package $rootPackage)
    {
        $this->uses = $uses;
        $this->rootPackage = $rootPackage;
    }

    private function displayPackage(string $package): string
    {
        $p = $this->rootPackage->findPackage($package);
        if (!empty($p)) {
            if (empty($p->package)) {
                return $p->getLogicalName();
            }
        }
        return $package; //外部のpackageはpackage表示
    }

    /**
     * @return string[] arrows.
     */
    public function getArrows(): array
    {
        $lines = [];
        $all = [];
        $packageRelations = [];
        foreach ($this->uses as $namespace => $us) {
            $packageNames = array_unique(array_map(function (PhpType $x) {
                return implode('.', $x->getNamespace());
            }, $us));
            // 対象となっているpackage以外のpackageは、即席で定義する必要がある。
            $all = array_unique(array_merge($all, $packageNames));
            $packageRelations[$namespace] = array_map(function (string $x) {
                return $this->displayPackage($x);
            }, $packageNames);
        }
        $externals = array_diff($all, array_keys($this->rootPackage->getTargetPackages()));
        if (count($externals) > 0) {
            $externalPackage = new PackageHierarchy($externals);
            $lines[] = $externalPackage->dump();
        }
        $arrows = [];
        foreach ($packageRelations as $package => $dependencies) {
            $package = $this->displayPackage($package);
            if (empty($package)) {
                continue;
            }
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

    /**
     * @param PackageArrow[] $arrows arrows.
     * @return PackageArrow[] merged arrows.
     */
    private function mergeBothSideArrow(array $arrows): array
    {
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
                if (!$m->isOpposite($b)) {
                    continue;
                }
                $m->bothSideArrow();
                break;
            }
            unset($m);
        }
        return $merged;
    }

    /**
     * @param PackageArrow[] $arrows arrows
     */
    private function existsUpsidedown(array $arrows, PackageArrow $arrow): bool
    {
        foreach ($arrows as $a) {
            if ($a->isOpposite($arrow)) {
                return true;
            }
        }
        return false;
    }
}
