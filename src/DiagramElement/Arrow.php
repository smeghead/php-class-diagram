<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Php\{
    PhpType,
    PhpClass,
};

abstract class Arrow
{
    protected string $figure = '..>';
    private PhpClass $from;
    private PhpType $to;

    public function __construct(PhpClass $from, PhpType $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): PhpClass
    {
        return $this->from;
    }

    public function getTo(): PhpType
    {
        return $this->to;
    }

    abstract public function toString(PhpClass $toClass): string;
}
