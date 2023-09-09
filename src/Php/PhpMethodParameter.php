<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

final class PhpMethodParameter
{
    private string $name;
    private PhpTypeExpression $type;

    public function __construct(string $name, PhpTypeExpression $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): PhpTypeExpression
    {
        return $this->type;
    }
}
