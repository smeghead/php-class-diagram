<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Options;

class Namespace_ {
    private Options $options;
    private static $namesAlreadyUsed = [];
    public static function init() {
        self::$namesAlreadyUsed = [];
    }

    public array $parents;
    public string $name;
    public string $fixedName = '';

    /** @var Namespace_[] namespaces */
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

    public function addEntry(array $paths, Entry $entry): void {
        if (count($paths) === 0) {
            $this->entries[] = $entry;
            return;
        }
        $dir = array_shift($paths);
        $ns = $this->findChild($dir);
        $ns->addEntry($paths, $entry);
        return;
    }

    private function findChild(string $dir): Namespace_ {
        if (empty($dir)) {
            return $this;
        }
        foreach ($this->children as $c) {
            if ($c->name === $dir) {
                return $c;
            }
        }
        // if not exists, generate Namespace_.
        $this->children[] = new self(array_merge($this->parents, [$this->name]), $dir, $this->options);
        return end($this->children);
    }

    /**
     * 重複したパッケージ名があると、PlantUMLでエラーが発生するので避ける
     */
    private function avoidDuplicateName(string $name) {
        if (in_array($name, self::$namesAlreadyUsed)) {
            return $this->avoidDuplicateName(sprintf('%s_', $name));
        }
        self::$namesAlreadyUsed[] = $name;
        $this->fixedName = $name;
        return $name;
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
            //$lines[] = sprintf('%spackage "%s" <<Rectangle>> {', $indent, $this->avoidDuplicateName($this->name));
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

    public function dumpPackages($level = 0): array {
        $indent = str_repeat('  ', $level);
        $lines = [];
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf('%spackage "%s" <<Rectangle>> {', $indent, $this->avoidDuplicateName($this->name));
        }
        foreach ($this->children as $n) {
            $lines = array_merge($lines, $n->dumpPackages($level + 1));
        }
        if ($this->name !== 'ROOT') {
            $lines[] = sprintf('%s}', $indent);
        }
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
            $uses = array_merge($uses, $e->class->getUses());
        }
        $acc[$this->fixedName] = $uses;
        foreach ($this->children as $n) {
            $acc = array_merge($acc, $n->getUses($acc));
        }
        return $acc;
    }
}
