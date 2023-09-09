<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement\Division;

final class DivisionColor
{
    private static \Generator $gen;
    public static function nextColor(): string
    {
        if (empty(self::$gen)) {
            self::$gen = generateDivisionColorGenerator();
        }
        self::$gen->next();
        return self::$gen->current();
    }
}

function generateDivisionColorGenerator(): \Generator
{
    $COLORS = [
        '#ffffcc',
        '#ccffcc',
        '#ffcccc',
        '#ccccff',
        '#ccffff',
        '#ffccff',
    ];
    // @phpstan-ignore-next-line
    while (true) {
        foreach ($COLORS as $c) {
            yield $c;
        }
    }
}
