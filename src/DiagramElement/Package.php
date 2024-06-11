<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Config\Options;

final class Package
{
    private Options $options;

    /** @var string[]  */
    public array $parents;
    public string $name;
    public string $package = '';

    /** @var Package[] packages */
    public array $children = [];
    /** @var Entry[] entries */
    public array $entries = [];

    /**
     * @param string[] $parents
     */
    public function __construct(array $parents, string $name, Options $options)
    {
        $this->parents = $parents;
        $this->name = $name;
        $this->options = $options;
    }

    public function getLogicalName(): string
    {
        return implode('.', array_merge(array_slice($this->parents, 1), [$this->name]));
    }

    /**
     * @param list<string> $paths
     */
    public function addEntry(array $paths, Entry $entry): string
    {
        if (count($paths) === 0) {
            if (empty($this->package)) {
                $this->package = implode('.', $entry->getClass()->getNamespace());
            }
            $this->entries[] = $entry;
            return $this->package;
        }
        $dir = array_shift($paths);
        $ns = $this->findChild($dir);
        $childNamespace = $ns->addEntry($paths, $entry);
        if (empty($this->package)) {
            //子のpackageを元に親のpackageを決定する。ROOTのpackageの決定
            $childParts = explode('.', $childNamespace);
            array_pop($childParts);
            $this->package = implode('.', $childParts);
        }
        return $this->package;
    }

    private function findChild(string $dir): Package
    {
        if (empty($dir)) {
            return $this;
        }
        foreach ($this->children as $c) {
            if ($c->name === $dir) {
                return $c;
            }
        }
        // if not exists, generate Package.
        $this->children[] = new self(array_merge($this->parents, [$this->name]), $dir, $this->options);
        return end($this->children);
    }

    /**
     * @return string[] diagram lines.
     */
    public function dump(int $level = 0): array
    {
        $indent = str_repeat('  ', $level);
        $lines = [];
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf(
                '%spackage %s as %s {',
                $indent,
                $this->name,
                $this->getLogicalName()
            );
        }
        $lines = array_merge($lines, ...array_map(fn($e) => $e->dump($level + 1), $this->entries));
        $lines = array_merge($lines, ...array_map(fn($n) => $n->dump($level + 1), $this->children));
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf('%s}', $indent);
        }
        return $lines;
    }

    /**
     * @return string[] diagram lines.
     */
    public function dumpPackages(int $level = 1): array
    {
        $indent = str_repeat('  ', $level);
        $lines = [];
        $target = empty($this->package) ? $this->getLogicalName() : $this->package;
        $targetElements = explode('.', $target);
        if ($level > 1) {
            $lines[] = sprintf(
                '%spackage %s {',
                $indent,
                end($targetElements)
            );
        } else {
            $lines[] = sprintf(
                '%spackage %s as %s {',
                $indent,
                $target,
                end($targetElements)
            );
        }
        $lines = array_merge($lines, ...array_map(fn($n) => $n->dumpPackages($level + 1), $this->children));
        $lines[] = sprintf('%s}', $indent);
        return $lines;
    }

    /**
     * @return Arrow[] 矢印一覧
     */
    public function getArrows(): array
    {
        $arrows = [];
        $arrows = array_merge($arrows, ...array_map(fn($e) => $e->getArrows(), $this->entries));
        $arrows = array_merge($arrows, ...array_map(fn($n) => $n->getArrows(), $this->children));
        $arrows = array_merge($arrows, ...array_map(fn($n) => $n->getUsingArrows(), $this->entries));

        return $arrows;
    }

    /**
     * @return Entry[] クラスなどの一覧
     */
    public function getEntries(): array
    {
        $entries = $this->entries;
        $entries = array_merge($entries, ...array_map(fn($n) => $n->getEntries(), $this->children));
        return $entries;
    }

    /**
     * @param array<string, \Smeghead\PhpClassDiagram\Php\PhpType[]> $acc list of uses.
     * @return array<string, \Smeghead\PhpClassDiagram\Php\PhpType[]> list of uses.
     */
    public function getUses(array $acc): array
    {
        $uses = [];
        $uses = array_merge($uses, ...array_map(fn($e) => $e->getClass()->getUses(), $this->entries));
        $package = empty($this->package) ? sprintf('%s.%s', implode('.', $this->parents), $this->name) : $this->package;
        $acc[$package] = $uses;
        $acc = array_merge($acc, ...array_map(fn($n) => $n->getUses($acc), $this->children));
        return $acc;
    }

    /**
     * 解析対象になっているpackage一覧を取得する。
     * @param array<string, string> $acc
     * @return array<string, string>
     */
    public function getTargetPackages(array $acc = []): array
    {
        $acc[$this->package] = $this->getLogicalName();
        foreach ($this->children as $n) {
            $acc = $n->getTargetPackages($acc);
        }
        return $acc;
    }

    public function findPackage(string $package): ?Package
    {
        return $this->recFindPackage($package);
    }

    public function is(string $package): bool
    {
        $segments = $this->parents;
        $segments[] = $this->name;
        return implode('.', $segments) === $package;
    }

    /**
     * @param string $package パッケージの表記(例: hoge.fuga)
     * @return ?Package
     */
    private function recFindPackage(string $package): ?Package
    {
        if ($this->is($package)) {
            return $this;
        }
        foreach ($this->children as $c) {
            $p = $c->recFindPackage($package);
            if (!empty($p)) {
                return $p;
            }
        }
        return null;
    }

    /**
     * @return string[] diagram lines.
     */
    public function dumpDivisions(int $level = 0): array
    {
        $indent = str_repeat('  ', $level);
        $lines = [];
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf(
                '%spackage %s as %s {',
                $indent,
                $this->name,
                $this->getLogicalName()
            );
        }
        $lines = array_merge($lines, ...array_map(fn($e) => $e->dumpDivisions($level + 1), $this->entries));
        $lines = array_merge($lines, ...array_map(fn($n) => $n->dumpDivisions($level + 1), $this->children));
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf('%s}', $indent);
        }
        return $lines;
    }
}
