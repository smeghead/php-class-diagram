<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Param;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Stmt\{
    ClassMethod,
    Property,
};

final class PhpProperty
{
    private string $name;
    private PhpTypeExpression $type;
    private PhpAccessModifier $accessModifier;
    private string $hooksState;

    private function __construct()
    {
    }

    public static function buildByProperty(Property $p, PhpClass $class): self
    {
        $instance = new self();
        $instance->name = $p->props[0]->name->toString();
        $instance->type = PhpTypeExpression::buildByVar($p, $class->getNamespace(), $class->getUses());
        $instance->accessModifier = new PhpAccessModifier($p);
        $instance->hooksState = self::parsePropertyHooks($p->hooks);
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
        $instance->hooksState = '';
        return $instance;
    }

    /**
     * @param list<PropertyHook>|null $hooks
     */
    private static function parsePropertyHooks(?array $hooks): string {
        if (empty($hooks)) {
            return '';
        }
        $propertyHooks = [];
        foreach ($hooks as $h) {
            $propertyHooks[] = sprintf(
                '%s%s',
                $h->byRef ? '&' : '',
                $h->name->name
            );
        }
        return sprintf('{%s}', implode('/', $propertyHooks));
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

    public function getHooksState(): string
    {
        return $this->hooksState;
    }
}
