<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Config\Options;

final class ClassIdentifier {
    public function __construct(private Options $options, private string $directory, private Entry $entry)
    {
    }

    public function getIdentifier(): string
    {
        $meta = $this->entry->getClass()->getClassType()->getMetaName();
        $classSummary = ($this->options->classNameSummary()
            ? $this->entry->getClass()->getDescription()
            : '');
        return sprintf(
            '%s "%s" as %s%s%s',
            $meta,
            $this->entry->getClass()->getClassType()->getName() . (empty($classSummary) ? '' : sprintf("\\n<b>%s</b>", $classSummary)),
            $this->entry->getClass()->getClassNameAlias(),
            $this->getLinkExpression($meta),
            $this->getRelTargetStyle()
        );
    }

    private function getLinkExpression(string $meta): string
    {
        if (empty($this->options->svgTopurl())) {
            return '';
        }
        $path = sprintf(
            '/%s%s.php',
            empty($this->directory) ? '' : sprintf('%s/', $this->directory),
            $this->entry->getClass()->getClassType()->getName());
        return sprintf(
            ' [[%s %s %s]]',
            $path,
            $this->entry->getClass()->getClassType()->getName(),
            ucfirst($meta)
        );
    }

    private function getRelTargetStyle(): string
    {
        $targets = [
            ...$this->options->relTargetsFrom(),
            ...$this->options->relTargetsTo(),
        ];
        return array_search($this->entry->getClass()->getClassType()->getName(), $targets) !== false
            ? ' #FFF0F5;line:740125;text:740125' : '';
    }
}