<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\DiagramElement;

use Smeghead\PhpClassDiagram\Php\ {
  PhpType,
  PhpClass,
};

abstract class Arrow {
    protected string $figure = '..>';
    public PhpClass $from;
    public PhpType $to;

    public function __construct(PhpClass $from, PhpType $to) {
        $this->from = $from;
        $this->to = $to;
    }

    abstract public function toString(PhpClass $toClass): string;
}
