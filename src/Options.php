<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

class Options {
    private array $opt;

    public function __construct(array $opt) {
        $this->opt = $opt;
    }

    public function help() {
        if (isset($this->opt['h'])) {
            return true;
        }
        if (isset($this->opt['help'])) {
            return true;
        }
        return false;
    }

    public function classProperties() {
        if (isset($this->opt['enable-class-properties'])) {
            return true;
        }
        if (isset($this->opt['disable-class-properties'])) {
            return false;
        }
        // default
        return false;
    }

    public function classMethods() {
        if (isset($this->opt['enable-class-methods'])) {
            return true;
        }
        if (isset($this->opt['disable-class-methods'])) {
            return false;
        }
        // default
        return false;
    }
}
