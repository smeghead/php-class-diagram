<?php declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Identifier;
use PhpParser\Node\NullableType;
use PhpParser\NodeAbstract;

class PhpTypeExpression {
    public const TYPE = 'type';
    public const RETURN_TYPE = 'return_type';

    /** @var PhpType[] */
    private array $types;

    public function __construct(NodeAbstract $stmt, string $targetType) {
        $type = $stmt->{$targetType};
        $nullable = false;
        if ($type instanceOf NullableType) {
            $type = $type->type;
            $nullable = true;
        }
        $parts = [];
        if ($type instanceOf Identifier) {
            $parts[] = $type->name;
        }
        $namespace = [];
        $typeName = array_pop($parts);
        $this->types[] = new PhpType($namespace, $stmt->getType(), $typeName ?? '', null, $nullable);
    }
    /**
     * @return PhpType[] types
     */
    public function getTypes(): array {
        return $this->types;
    }
}