<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Config;

final class Options
{
    /** @var array<string, mixed> */
    private array $opt;

    public const PHP5 = 'php5';
    public const PHP7 = 'php7';
    public const PHP8 = 'php8';

    public const DIAGRAM_CLASS = 'class';
    public const DIAGRAM_PACKAGE = 'package';
    public const DIAGRAM_JIG = 'jig';
    public const DIAGRAM_DIVISION = 'division';

    /**
     * @param array<string, mixed> $opt Option array
     */
    public function __construct(array $opt)
    {
        $this->opt = $opt;
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
            return self::DIAGRAM_DIVISION;
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

    /**
     * @return string specified svgTopurl
     */
    public function svgTopurl(): string
    {
        if ( ! isset($this->opt['svg-topurl'])) {
            return '';
        }
        return $this->opt['svg-topurl'];
    }

    public function hidePrivateProperties(): bool
    {
        if (isset($this->opt['hide-private-properties'])) {
            return true;
        }
        if (isset($this->opt['hide-private'])) {
            return true;
        }
        return false;
    }

    public function hidePrivateMethods(): bool
    {
        if (isset($this->opt['hide-private-methods'])) {
            return true;
        }
        if (isset($this->opt['hide-private'])) {
            return true;
        }
        return false;
    }
}
