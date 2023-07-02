<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Config;

class Options
{
    private array $opt;

    const PHP5 = 'php5';
    const PHP7 = 'php7';
    const PHP8 = 'php8';

    const DIAGRAM_CLASS = 'class';
    const DIAGRAM_PACKAGE = 'package';
    const DIAGRAM_JIG = 'jig';
    const DIAGRAM_DIVSION = 'division';

    const TARGET_PLANTUML = 'plantuml';
    const TARGET_MERMAID = 'mermaid';

    public function __construct(array $opt)
    {
        $this->opt = $opt;
        if ($this->target() === self::TARGET_MERMAID) {
            if ($this->diagram() !== self::DIAGRAM_CLASS) {
                throw new ConfigException('not supported.');
            }
        }
    }

    public function help(): bool
    {
        if (isset($this->opt['h'])) {
            return true;
        }
        if (isset($this->opt['help'])) {
            return true;
        }
        return false;
    }

    public function diagram(): string
    {
        if (isset($this->opt['class-diagram'])) {
            return self::DIAGRAM_CLASS;
        }
        if (isset($this->opt['package-diagram'])) {
            return self::DIAGRAM_PACKAGE;
        }
        if (isset($this->opt['jig-diagram'])) {
            return self::DIAGRAM_JIG;
        }
        if (isset($this->opt['division-diagram'])) {
            return self::DIAGRAM_DIVSION;
        }
        // default
        return self::DIAGRAM_CLASS;
    }

    public function classProperties(): bool
    {
        if (isset($this->opt['enable-class-properties'])) {
            return true;
        }
        if (isset($this->opt['disable-class-properties'])) {
            return false;
        }
        // default
        return true;
    }

    public function classMethods(): bool
    {
        if (isset($this->opt['enable-class-methods'])) {
            return true;
        }
        if (isset($this->opt['disable-class-methods'])) {
            return false;
        }
        // default
        return true;
    }

    public function classNameSummary(): bool
    {
        if (isset($this->opt['enable-class-name-summary'])) {
            return true;
        }
        if (isset($this->opt['disable-class-name-summary'])) {
            return false;
        }
        // default
        return true;
    }

    public function phpVersion(): string
    {
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

    /**
     * @return string[] specified headers
     */
    public function headers(): array
    {
        $headers = [];
        if (isset($this->opt['header'])) {
            $headers = $this->opt['header'];
            if (!is_array($headers)) {
                $headers = [$headers];
            }
        }
        return $headers;
    }

    /**
     * @return string[] specified includes
     */
    public function includes(): array
    {
        $includes = [];
        if (isset($this->opt['include'])) {
            $includes = $this->opt['include'];
            if (!is_array($includes)) {
                $includes = [$includes];
            }
        } else {
            $includes[] = '*.php';
        }
        return $includes;
    }

    /**
     * @return string[] specified excludes
     */
    public function excludes(): array
    {
        $excludes = [];
        if (isset($this->opt['exclude'])) {
            $excludes = $this->opt['exclude'];
            if (!is_array($excludes)) {
                $excludes = [$excludes];
            }
        }
        return $excludes;
    }

    public function target(): string {
        if (isset($this->opt['mermaid'])) {
            return self::TARGET_MERMAID;
        }
        return self::TARGET_PLANTUML;
    }
}
