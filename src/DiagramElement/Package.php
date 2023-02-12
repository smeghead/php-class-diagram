<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Config\Options;

class Package {
    private Options $options;

    public array $parents;
    public string $name;
    public string $package = '';

    /** @var Package[] packages */
    public array $children = [];
    /** @var Entry[] entries */
    public array $entries = [];

    public function __construct(array $parents, string $name, Options $options) {
        $this->parents = $parents;
        $this->name = $name;
        $this->options = $options;
    }

    public function getLogicalName(): string {
        return implode('.', array_merge(array_slice($this->parents, 1), [$this->name]));
    }

    public function addEntry(array $paths, Entry $entry): string {
        if (count($paths) === 0) {
            if (empty($this->package)) {
                $this->package = implode('.', $entry->getClass()->getClassType()->getNamespace());
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

    private function findChild(string $dir): Package {
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

    public function dump($level = 0): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf(
                '%spackage %s as %s <<Rectangle>> {',
                $indent,
                $this->name,
                $this->getLogicalName()
            );
        }
        foreach ($this->entries as $e) {
            $lines = array_merge($lines, $e->dump($level + 1));
        }
        foreach ($this->children as $n) {
            $lines = array_merge($lines, $n->dump($level + 1));
        }
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf('%s}', $indent);
        }
        return $lines;
    }

    public function dumpPackages($level = 1): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        $lines[] = sprintf(
            '%spackage %s as %s {',
            $indent,
            $this->name === 'ROOT' ? (empty($this->package) ? 'ROOT': $this->package) : $this->name,
            $this->getLogicalName()
        );
        foreach ($this->children as $n) {
            $lines = array_merge($lines, $n->dumpPackages($level + 1));
        }
        $lines[] = sprintf('%s}', $indent);
        return $lines;
    }

    /**
     * @return Arrow[] 矢印一覧
     */
    public function getArrows(): array {
        $arrows = [];
        foreach ($this->entries as $e) {
            $arrows = array_merge($arrows, $e->getArrows());
        }
        foreach ($this->children as $n) {
            $arrows = array_merge($arrows, $n->getArrows());
        }
        return $arrows;
    }

    /**
     * @return Entry[] クラスなどの一覧
     */ 
    public function getEntries(): array {
        $entries = $this->entries;
        foreach ($this->children as $n) {
            $entries = array_merge($entries, $n->getEntries());
        }
        return $entries;
    }

    /**
     * @return array useの一覧
     */ 
    public function getUses($acc): array {
        $uses = [];
        foreach ($this->entries as $e) {
            $uses = array_merge($uses, $e->getClass()->getUses());
        }
        $acc[$this->package] = $uses;
        foreach ($this->children as $n) {
            $acc = array_merge($acc, $n->getUses($acc));
        }
        return $acc;
    }

    /**
     * 解析対象になっているpackage一覧を取得する。
     */
    public function getTargetPackages($acc = []) {
        $acc[$this->package] = $this->getLogicalName();
        foreach ($this->children as $n) {
            $acc = $n->getTargetPackages($acc);
        }
        return $acc;
    }
}
