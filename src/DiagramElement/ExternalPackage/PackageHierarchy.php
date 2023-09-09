<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\ExternalPackage;

final class PackageHierarchy
{
    private PackageNode $root;

    /**
     * @param string[] $externalPackages external package list
     */
    public function __construct(array $externalPackages)
    {
        $this->root = new PackageNode('root');
        foreach ($externalPackages as $p) {
            $this->root->register(explode('.', $p));
        }
    }

    public function dump(): string
    {
        $elements = [];
        foreach ($this->root->getChildren() as $c) {
            $elements[] = $c->dump(1);
        }
        return implode("\n", $elements);
    }
}

class PackageNode
{
    private string $name;
    /** @var PackageNode[] children */
    private array $children = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return PackageNode[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param string[] $packages
     */
    public function register(array $packages): void
    {
        if (count($packages) === 0) {
            return;
        }
        $next = array_shift($packages);
        foreach ($this->children as $c) {
            if ($c->name === $next) {
                $c->register($packages);
                return;
            }
        }
        $new = new PackageNode($next);
        $new->register($packages);
        $this->children[] = $new;
    }

    public function dump(int $indent): string
    {
        if (empty($this->name)) {
            return ''; // Do not display packages with empty package names
        }
        $lines = [];
        $lines[] = sprintf('%spackage %s #DDDDDD {', str_repeat('  ', $indent), $this->name);
        foreach ($this->children as $c) {
            $lines[] = $c->dump($indent + 1);
        }
        $lines[] = sprintf('%s}', str_repeat('  ', $indent));
        return implode("\n", $lines);
    }
}
