<?php declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Identifier;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PhpParser\NodeAbstract;

class PhpTypeExpression {
    public const TYPE = 'type';
    public const RETURN_TYPE = 'return_type';

    /** @var PhpType[] */
    private array $types;

    public function __construct(NodeAbstract $stmt, string $targetType) {
        $type = $stmt->{$targetType};
        if ($type instanceOf UnionType) {
            foreach ($type->types as $t) {
                $this->types[] = $this->parseType($t);
            }
        } else {
            $this->types[] = $this->parseType($type);
        }
    }

    private function parseType(Property|Identifier|NullableType $type) {
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
        return new PhpType($namespace, $type->getType(), $typeName ?? '', null, $nullable);

    }
    /**
     * @return PhpType[] types
     */
    public function getTypes(): array {
        return $this->types;
    }
}