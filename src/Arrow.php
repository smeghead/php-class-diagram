<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

class Arrow {
    public string $from;
    public string $to;

    public function __construct(string $from, string $to) {
        $this->from = $from;
        $this->to = $to;
    }
}
