<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Config;

class Options {
    private array $opt;

    const PHP5 = 'php5';
    const PHP7 = 'php7';
    const PHP8 = 'php8';

    const DIAGRAM_CLASS = 'class';
    const DIAGRAM_PACKAGE = 'package';
    const DIAGRAM_JIG = 'jig';

    public function __construct(array $opt) {
        $this->opt = $opt;
    }

    public function help(): bool {
        if (isset($this->opt['h'])) {
            return true;
        }
        if (isset($this->opt['help'])) {
            return true;
        }
        return false;
    }

    public function diagram(): string {
        if (isset($this->opt['class-diagram'])) {
            return self::DIAGRAM_CLASS;
        }
        if (isset($this->opt['package-diagram'])) {
            return self::DIAGRAM_PACKAGE;
        }
        if (isset($this->opt['jig-diagram'])) {
            return self::DIAGRAM_JIG;
        }
        // default
        return self::DIAGRAM_CLASS;
    }

    public function classProperties(): bool {
        if (isset($this->opt['enable-class-properties'])) {
            return true;
        }
        if (isset($this->opt['disable-class-properties'])) {
            return false;
        }
        // default
        return false;
    }

    public function classMethods(): bool {
        if (isset($this->opt['enable-class-methods'])) {
            return true;
        }
        if (isset($this->opt['disable-class-methods'])) {
            return false;
        }
        // default
        return false;
    }

    public function phpVersion(): string {
        if (isset($this->opt['php5'])) {
            return self::PHP5;
        }
        if (isset($this->opt['php7'])) {
            return self::PHP7;
        }
        if (isset($this->opt['php8'])) {
            return self::PHP8;
        }
        // default
        return self::PHP7;
    }
}
