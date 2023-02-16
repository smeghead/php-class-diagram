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
    public const FOR_TEST = 'for_test';

    /** @var string  */
    private string $docString = '';
    /** @var PhpType[] */
    private array $types;
    /** @var PhpType[] */
    private array $uses;

    /**
     * @param NodeAbstract $stmt 対象のツリー
     * @param string $targetType 
     * @param string[] $currentNamespace
     * @param string $docString
     * @param PhpType[] $uses
     */
    private function __construct(NodeAbstract $stmt, string $targetType, array $currentNamespace, string $docString, array $uses) {
        if ( ! in_array($targetType, [self::VAR, self::PARAM, self::RETURN_TYPE, self::FOR_TEST])) {
            throw new \Exception('invalid tag.');
        }
        if ($targetType === self::FOR_TEST) {
            return; // 単体テストのため仕方なく空のインスタンスを生成する方法を用意します。
        }
        $this->uses = $uses;

        $type = $stmt->{$targetType === self::RETURN_TYPE ? 'returnType' : 'type'};
        if ( ! empty($docString)) {
            foreach (explode('|', $docString) as $typeString) {
                $this->types[] = $this->parseType($type, $currentNamespace, $typeString);
            }
        } else if ($type instanceof UnionType) {
            foreach ($type->types as $t) {
                $this->types[] = $this->parseType($t, $currentNamespace);
            }
        } else {
            $this->types[] = $this->parseType($type, $currentNamespace);
        }
    }

    /**
     * @param NodeAbstract $stmt
     * @param string[] $currentNamespace
     * @param PhpType[] $uses
     * @return self
     */
    public static function buildByVar(
        NodeAbstract $stmt,
        array $currentNamespace,
        array $uses
    ): self {
        $doc = $stmt->getDocComment();
        $typeString = '';
        if ($doc instanceof Doc) {
            $docString = $doc->getText();
            if (preg_match(sprintf('/@%s\s+(\S+)(\b|\s).*/', 'var'), $docString, $matches)) {
                $typeString = $matches[1];
            }
        }
        return new self($stmt, self::VAR, $currentNamespace, $typeString, $uses);
    }

    /**
     * @param NodeAbstract $stmt
     * @param string[] $currentNamespace
     * @param string $docString
     * @param PhpType[] $uses
     * @return self
     */
    public static function buildByMethodParam(
        NodeAbstract $stmt,
        array $currentNamespace,
        string $docString,
        string $paramName,
        array $uses
    ): self {
        $typeString = '';
        if (!empty($docString)) {
            if (preg_match(sprintf('/@%s\s+(\S+)(\b|\s)\s*\$%s.*/', 'param', $paramName), $docString, $matches)) {
                $typeString = $matches[1];
            }
        }
        return new self($stmt, self::PARAM, $currentNamespace, $typeString, $uses);
    }

    /**
     * @param NodeAbstract $stmt
     * @param string[] $currentNamespace
     * @param PhpType[] $uses
     * @return self
     */
    public static function buildByMethodReturn(
        NodeAbstract $stmt,
        array $currentNamespace,
        array $uses
    ): self {
        $doc = $stmt->getDocComment();
        $typeString = '';
        if ($doc instanceof Doc) {
            $docString = $doc->getText();
            if (preg_match(sprintf('/@%s\s+(\S+)(\b|\s).*/', 'return'), $docString, $matches)) {
                $typeString = $matches[1];
            }
        }
        return new self($stmt, self::RETURN_TYPE, $currentNamespace, $typeString, $uses);
    }

    /**
     * 単体テスト用factory
     */
    public static function buildByPhpType(PhpType $type): self {
        $instance = new self(new Name('dummy'), self::FOR_TEST, [], '', []);
        $instance->types[] = $type;
        return $instance;
    }

    /**
     * @param Property|Identifier|NullableType|Name|UnionType|null $type 型を表すAST (UnionTypeが指定されて呼び出される時は、typeStringで判断する時なので型判定には使われない)
     * @param string[] $currentNamespace 名前空間配列
     * @param ?string $typeString コメントの型表記
     */
    private function parseType(Property|Identifier|NullableType|Name|UnionType|null $type, array $currentNamespace, ?string $typeString = '') {
        $parts = [];
        if (!empty($typeString)) {
            $primitiveTypes = [
                'null',
                'bool',
                'int',
                'float',
                'string',
                'array',
                'object',
                'callable',
                'resource',                
            ];
            if (in_array($typeString, $primitiveTypes)) {
                $parts = [$typeString]; // primitive typeは、namespaceを付与しない。
            } else {
                if (mb_substr($typeString, 0, 1) === '\\') {
                    $docString = mb_substr($typeString, 1);
                    $parts = explode('\\', $docString);
                } else {
                    // usesを検索して適切なnamespaceを探す必要がある。
                    $targets = array_values(array_filter($this->uses, function(PhpType $t) use($typeString) {
                        $xParts = explode('\\', $typeString);
                        $name = end($xParts);
                        // docString で配列が指定されていた場合は、[] を除外して比較する。
                        return preg_replace('/\[\]$/', '', $name) === $t->getName();
                    }));
                    if (count($targets) > 0) {
                        $parts = array_merge(
                            $targets[0]->getNamespace(),
                            [sprintf('%s%s', $targets[0]->getName(), preg_match('/\[\]$/', $typeString) ? '[]' : '')]);
                    } else {
                        $parts = array_merge($currentNamespace, explode('\\', $typeString));
                    }
                }
            }
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
                $typeParts = $type->parts;
                // usesを検索して適切なnamespaceを探す必要がある。
                $targets = array_values(array_filter($this->uses, function(PhpType $t) use($typeParts) {
                    $name = end($typeParts);
                    return $name === $t->getName();
                }));
                if (count($targets) > 0) {
                    $parts = array_merge($targets[0]->getNamespace(), [end($typeParts)]);
                } else {
                    $parts = array_merge($currentNamespace, $type->parts);
                }
            }
        }
        $typeName = array_pop($parts);
        return new PhpType($parts, empty($type) ? '' : $type->getType(), $typeName ?? '', null, $nullable);
    }

    /**
     * @return PhpType[] types
     */
    public function getTypes(): array {
        return $this->types;
    }

    public function getName(): string {
        $types = [];
        foreach ($this->types as $type) {
            $types[] = sprintf('%s%s', $type->getNullable() ? '?' : '', $type->getName());
        }
        return implode('|', $types);
    }
}