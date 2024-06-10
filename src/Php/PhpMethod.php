<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\{
    Param,
};
use PhpParser\Node\Stmt\{
    ClassMethod,
};

final class PhpMethod
{
    private string $name;
    private PhpTypeExpression $type;
    /** @var PhpMethodParameter[] パラメータ一覧 */
    private array $params;
    private PhpAccessModifier $accessModifier;

    public function __construct(ClassMethod $method, PhpClass $class)
    {
        $params = array_map(function (Param $x) use ($class, $method) {
            /** @var string $varName */
            $varName = $x->var->name; /** @phpstan-ignore-line */
            $type = PhpTypeExpression::buildByMethodParam(
                $x,
                $class->getNamespace(),
                $method,
                $varName,
                $class->getUses()
            );
            return new PhpMethodParameter($varName, $type);
        }, $method->getParams());
        $this->name = $method->name->toString();
        $this->type = $this->getTypeFromMethod($method, $class);
        $this->params = $params;
        $this->accessModifier = new PhpAccessModifier($method);
    }

    private function getTypeFromMethod(ClassMethod $method, PhpClass $class): PhpTypeExpression
    {
        return PhpTypeExpression::buildByMethodReturn($method, $class->getNamespace(), $class->getUses());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): PhpTypeExpression
    {
        return $this->type;
    }

    /**
     * @return PhpMethodParameter[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getAccessModifier(): PhpAccessModifier
    {
        return $this->accessModifier;
    }
}
