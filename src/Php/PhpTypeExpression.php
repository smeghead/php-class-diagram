<?php declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PhpParser\NodeAbstract;

class PhpTypeExpression {
    public const TYPE = 'type';
    public const RETURN_TYPE = 'return_type';

    /** @var PhpType[] */
    private array $types;

    public function __construct(NodeAbstract $stmt, string $targetType, array $currentNamespace) {
        $type = $stmt->{$targetType};
        if ($type instanceOf UnionType) {
            foreach ($type->types as $t) {
                $this->types[] = $this->parseType($t, $currentNamespace);
            }
        } else {
            $this->types[] = $this->parseType($type, $currentNamespace);
        }
    }

    /**
     * @param Property|Identifier|NullableType|Name $type 型を表すAST
     * @param string[] $currentNamespace 名前空間配列
     */
    private function parseType(Property|Identifier|NullableType|Name $type, array $currentNamespace) {
        $nullable = false;
        if ($type instanceOf NullableType) {
            $type = $type->type;
            $nullable = true;
        }
        $parts = [];
        if ($type instanceOf Identifier) {
            $parts[] = $type->name;
        } else if ($type instanceOf FullyQualified) {
            $parts = $type->parts;
        } else if ($type instanceOf Name) {
            $parts = array_merge($currentNamespace, $type->parts);
        }
        $typeName = array_pop($parts);
        return new PhpType($parts, $type->getType(), $typeName ?? '', null, $nullable);
    }
    
    /**
     * @return PhpType[] types
     */
    public function getTypes(): array {
        return $this->types;
    }
}