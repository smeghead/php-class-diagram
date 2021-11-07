<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

abstract class Arrow {
    protected string $figure = '..>';
    public string $from;
    public string $to;

    public function __construct(string $from, string $to) {
        $this->from = $from;
        $this->to = $to;
    }

    abstract public function toString(): string;
}
