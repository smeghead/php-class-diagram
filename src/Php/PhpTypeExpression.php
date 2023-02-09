<?php declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Comment\Doc;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use PhpParser\NodeAbstract;

class PhpTypeExpression {
    public const VAR = 'var';
    public const PARAM = 'param';
    public const RETURN_TYPE = 'return';

    /** @var string  */
    private string $docString = '';
    /** @var PhpType[] */
    private array $types;

    public function __construct(NodeAbstract $stmt, string $targetType, array $currentNamespace) {
        if ( ! in_array($targetType, [self::VAR, self::PARAM, self::RETURN_TYPE])) {
            throw new \Exception('invalid tag.');
        }
        $doc = $stmt->getDocComment();
        if ($doc instanceof Doc) {
            $docString = $doc->getText();
            if (preg_match(sprintf('/@%s\s+(\S+)(\b|\s).*/', $targetType), $docString, $matches)) {
                var_dump($docString);
                $this->docString = $matches[1];
            }
        }

        $type = $stmt->{$targetType === self::RETURN_TYPE ? 'returnType' : 'type'};
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
        $parts = [];
        if (!empty($this->docString)) {
            if (mb_substr($this->docString, 0, 1) === '\\') {
                $docString = mb_substr($this->docString, 1);
            } else {
                var_dump($this->docString);
                $docString = sprintf('%s\\%s', implode('\\', $currentNamespace), $this->docString);
                var_dump($docString);
            }
            $parts = explode('\\', $docString);
        }
        $nullable = false;
        if (count($parts) === 0) { // docCommentから取得できない時には、$typeを解析する。
            if ($type instanceOf NullableType) {
                $type = $type->type;
                $nullable = true;
            }
            if ($type instanceOf Identifier) {
                $parts[] = $type->name;
            } else if ($type instanceOf FullyQualified) {
                $parts = $type->parts;
            } else if ($type instanceOf Name) {
                $parts = array_merge($currentNamespace, $type->parts);
            }
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