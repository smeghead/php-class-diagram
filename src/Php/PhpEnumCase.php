<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\{
    EnumCase,
};
use Smeghead\PhpClassDiagram\Php\Doc\PhpDocComment;

final class PhpEnumCase
{
    private string $name;
    private PhpDocComment $doc;

    public function __construct(EnumCase $e)
    {
        $this->name = $e->name->name;
        $this->doc = new PhpDocComment($e);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDocString(): string
    {
        return $this->doc->getDescription();
    }

}
