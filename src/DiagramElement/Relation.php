<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Config\Options;

final class Relation
{
    private Options $options;
    private Package $package;

    private RelationsFilter $relationsFilter;

    /**
     * @param Entry[] $entries
     */
    public function __construct(array $entries, Options $options)
    {
        $this->options = $options;
        $this->relationsFilter = new RelationsFilter($options);
        $this->package = new Package([], 'ROOT', $options);
        foreach ($entries as $e) {
            /** @var list<string> $paths */
            $paths = preg_split('/[\\\\\/]/', $e->getDirectory());
            $this->package->addEntry($paths, $e);
        }
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    /**
     * @return string[] diagram lines.
     */
    public function dump(): array
    {
        $lines = ['@startuml class-diagram'];
        if ( ! empty($this->options->svgTopurl())) {
            $lines[] = '  skinparam svgLinkTarget _blank';
            $lines[] = sprintf('  skinparam topurl %s', $this->options->svgTopurl());
        }
        $lines = array_merge($lines, array_map(function ($x) {
            return '  ' . $x;
        }, $this->options->headers()));
        $lines = array_merge($lines, $this->package->dump());
        $lines = array_merge($lines, $this->getRelations());
        $lines[] = '@enduml';

        return $lines;
    }

    /**
     * @return string[] diagram lines.
     */
    public function getRelations(): array
    {
        $entries = $this->package->getEntries();
        $relation_expressions = array_map(function (Arrow $x) use ($entries) {
            foreach ($entries as $e) {
                if ($e->getClass()->getClassType()->equals($x->getTo())) {
                    return $x->toString($e->getClass());
                }
            }
            return null;
        }, $this->package->getArrows());

        $relation_expressions = array_filter($relation_expressions);

        $relation_expressions = $this->relationsFilter->filterRelations($relation_expressions);

        sort($relation_expressions);
        $relation_expressions = $this->relationsFilter->addRemoveUnlinkedDirective($relation_expressions);
        return array_values(array_unique($relation_expressions));
    }

    /**
     * @return string[] diagram lines.
     */
    public function dumpPackages(): array
    {
        $lines = ['@startuml package-related-diagram'];
        $lines = array_merge($lines, $this->options->headers());
        $lines = array_merge($lines, $this->package->dumpPackages());
        $uses = $this->getUses();
        $packageRelations = new PackageRelations($uses, $this->package);
        $lines = array_merge($lines, $packageRelations->getArrows());
        $lines[] = '@enduml';

        return $lines;
    }

    /**
     * @return array<string, \Smeghead\PhpClassDiagram\Php\PhpType[]> uses.
     */
    public function getUses(): array
    {
        return $this->package->getUses([]);
    }

    /**
     * @return string[] diagram lines.
     */
    public function dumpDivisions(): array
    {
        $lines = ['@startuml division-diagram'];
        $lines = array_merge($lines, array_map(function ($x) {
            return '  ' . $x;
        }, $this->options->headers()));
        $lines = array_merge($lines, $this->package->dumpDivisions());
        $lines[] = '@enduml';

        return $lines;
    }
}
