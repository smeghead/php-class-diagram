<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Param;
use PhpParser\Node\Stmt\{
    ClassMethod,
    Property,
};

final class PhpProperty
{
    private string $name;
    private PhpTypeExpression $type;
    private PhpAccessModifier $accessModifier;

    private function __construct()
    {
    }

    public static function buildByProperty(Property $p, PhpClass $class): self
    {
        $instance = new self();
        $instance->name = $p->props[0]->name->toString();
        $instance->type = PhpTypeExpression::buildByVar($p, $class->getNamespace(), $class->getUses());
        $instance->accessModifier = new PhpAccessModifier($p);
        return $instance;
    }

    public static function buildByParam(Param $param, ClassMethod $method, PhpClass $class): self
    {
        $instance = new self();
        /** @var string $varName */
        $varName = $param->var->name; // @phpstan-ignore-line
        $instance->name = $varName;
        $instance->type = PhpTypeExpression::buildByMethodParam(
            $param,
            $class->getNamespace(),
            $method,
            $varName,
            $class->getUses()
        );
        $instance->accessModifier = new PhpAccessModifier($param);
        return $instance;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): PhpTypeExpression
    {
        return $this->type;
    }

    public function getAccessModifier(): PhpAccessModifier
    {
        return $this->accessModifier;
    }
}
