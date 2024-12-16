<?php

namespace Smeghead\PhpClassDiagram\DiagramElement;

use InvalidArgumentException;
use Smeghead\PhpClassDiagram\Config\Options;
use function preg_match;

class RelationsFilter {

    private int $maxDepth;
    /**
     * @var string[]
     */
    private array $relationExpressions;
    private bool $removeUnlinked = false;

    public function __construct(private Options $options)
    {
    }

    /**
     * @param list<string> $relation_expressions
     * @return list<string>
     */
    public function filterRelations(array $relation_expressions): array
    {
        $output = [];
        $fromClasses = $this->options->relTargetsFrom();
        $toClasses = $this->options->relTargetsTo();

        $this->maxDepth = $this->options->relTargetsDepth() - 1;
        $this->relationExpressions = $relation_expressions;

        if ([] === $fromClasses && [] === $toClasses) {
            return $relation_expressions;
        }

        if ([] !== $fromClasses) {
            $output = array_merge($output, $this->filterClasses($fromClasses, 'out'));
            $this->removeUnlinked = true;
        }

        if ([] !== $toClasses) {
            $output = array_merge($output, $this->filterClasses($toClasses, 'in'));
            $this->removeUnlinked = true;
        }

        return $output;
    }

    /**
     * @param list<string> $relation_expressions
     * @return list<string>
     */
    public function addRemoveUnlinkedDirective(array $relation_expressions): array
    {
        if ($this->removeUnlinked) {
            $relation_expressions[] = '  remove @unlinked';
        }
        return $relation_expressions;
    }

    /**
     * @param array<string> $filteredClasses
     * @return array<string>
     */
    public function filterClasses(array $filteredClasses, string $direction): array
    {
        $currentDepth = 0;
        /** @var array<string> $matches */
        $matches = [];
        do {
            $oldMatches = $matches;
            foreach ($matches as $match) {
                $parts = explode(' ', trim($match));
                $filteredClasses[] = $direction === 'out' ?
                    end($parts) :
                    array_shift($parts)
                ;
            }
            $matches = array_filter($this->relationExpressions, function ($line) use ($filteredClasses, $direction) {
                $line = str_replace(['"1" ', '"*" '], '', $line);
                $line = trim($line);
                foreach ($filteredClasses as $filteredClass) {
                    if (1 === preg_match($this->getFilteringRegex($filteredClass, $direction), $line)) {
                        return true;
                    }
                }
                return false;
            });
            $matches = array_unique($matches);
            $filteredClasses = array_unique($filteredClasses);
        } while (++$currentDepth <= $this->maxDepth && count(array_diff($matches, $oldMatches)) > 0);

        return $matches;
    }

    function getFilteringRegex(string $filteredClass, string $direction): string
    {
        $filteredClass = str_replace('*', '.*?', $filteredClass);

        if (!in_array($direction, ['out', 'in'])) {
            throw new InvalidArgumentException("Invalid direction '$direction'");
        }

        return match ($direction) {
            'in' => "/.*?> ({$filteredClass}$|[\w]+_{$filteredClass}$)/",
            'out' => "/^({$filteredClass}|^[\w]+_{$filteredClass}) .*?>.*?/",
        };
    }
}
