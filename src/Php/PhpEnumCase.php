<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\{
    EnumCase,
};

class PhpEnumCase
{
    protected string $name;
    protected ?Doc $doc;

    public function __construct(EnumCase $e)
    {
        $this->name = $e->name->name;
        $this->doc = $e->getDocComment();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDocString(): string
    {
        if (empty($this->doc)) {
            return '';
        }
        $str = $this->doc->getText();
        $str = preg_replace('/\/\*\*?\s*(.*)\s*\*\//s', '$1', $str);
        $str = preg_replace('/^\s*\**\s*/', '', $str);
        $str = str_replace(["\r\n", "\r", "\n"], "\n", $str);
        $lines = explode("\n", $str);
        return trim(array_shift($lines));
    }

}
