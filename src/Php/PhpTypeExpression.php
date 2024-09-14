<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use Smeghead\PhpClassDiagram\Php\Doc\PhpDocComment;

final class PhpTypeExpression
{
    public const VAR = 'var';
    public const PARAM = 'param';
    public const RETURN_TYPE = 'return';
    public const FOR_TEST = 'for_test';

    /** @var PhpType[] */
    private array $types;
    /** @var PhpType[] */
    private array $uses;

    /**
     * @param Node|Param|Property|ClassMethod|Name $stmt 対象のツリー
     * @param string $targetType 
     * @param string[] $currentNamespace
     * @param string $docString
     * @param PhpType[] $uses
     */
    private function __construct(Node $stmt, string $targetType, array $currentNamespace, string $docString, array $uses)
    {
        if (!in_array($targetType, [self::VAR, self::PARAM, self::RETURN_TYPE, self::FOR_TEST])) {
            throw new \Exception('invalid tag.');
        }
        if ($targetType === self::FOR_TEST) {
            return; // 単体テストのため仕方なく空のインスタンスを生成する方法を用意します。
        }
        $this->uses = $uses;

        // @phpstan-ignore-next-line
        $type = $stmt->{$targetType === self::RETURN_TYPE ? 'returnType' : 'type'};
        if (!empty($docString)) {
            $docString = preg_replace('/^\((.*)\)$/', '$1', $docString);
            $typeStrings = array_map(function(string $x){
                return trim($x);
            }, explode('|', $docString));
            foreach ($typeStrings as $typeString) {
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
     * @param Node|Param|Property|ClassMethod $stmt 対象のツリー
     * @param string[] $currentNamespace
     * @param PhpType[] $uses
     * @return self
     */
    public static function buildByVar(
        Node $stmt,
        array $currentNamespace,
        array $uses
    ): self {
        $doc = new PhpDocComment($stmt);
        $typeString = $doc->getVarTypeName();
        return new self($stmt, self::VAR, $currentNamespace, $typeString, $uses);
    }

    /**
     * @param Node|Param|Property|ClassMethod $stmt 対象のツリー
     * @param string[] $currentNamespace
     * @param ClassMethod $method
     * @param PhpType[] $uses
     * @return self
     */
    public static function buildByMethodParam(
        Node $stmt,
        array $currentNamespace,
        Node $method,
        string $paramName,
        array $uses
    ): self {
        $doc = new PhpDocComment($method);
        $typeString = $doc->getParamTypeName($paramName);
        return new self($stmt, self::PARAM, $currentNamespace, $typeString, $uses);
    }

    /**
     * @param Param|Property|ClassMethod $stmt 対象のツリー
     * @param string[] $currentNamespace
     * @param PhpType[] $uses
     * @return self
     */
    public static function buildByMethodReturn(
        Node $stmt,
        array $currentNamespace,
        array $uses
    ): self {
        $doc = new PhpDocComment($stmt);
        $typeString = $doc->getReturnTypeName();
        return new self($stmt, self::RETURN_TYPE, $currentNamespace, $typeString, $uses);
    }

    /**
     * 単体テスト用factory
     */
    public static function buildByPhpType(PhpType $type): self
    {
        $instance = new self(new Name('dummy'), self::FOR_TEST, [], '', []);
        $instance->types[] = $type;
        return $instance;
    }

    /**
     * @param Property|Identifier|NullableType|Name|UnionType|IntersectionType|null $type 型を表すAST (UnionTypeが指定されて呼び出される時は、typeStringで判断する時なので型判定には使われない)
     * @param string[] $currentNamespace 名前空間配列
     * @param ?string $typeString コメントの型表記
     */
    private function parseType(Property|Identifier|NullableType|Name|UnionType|IntersectionType|null $type, array $currentNamespace, ?string $typeString = ''): PhpType
    {
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
                    $targets = array_values(array_filter($this->uses, function (PhpType $t) use ($typeString) {
                        $xParts = explode('\\', $typeString);
                        $name = end($xParts);
                        // docString で配列が指定されていた場合は、[] を除外して比較する。
                        return preg_replace('/\[\]$/', '', $name) === $t->getName();
                    }));
                    if (count($targets) > 0) {
                        $parts = array_merge(
                            $targets[0]->getNamespace(),
                            [sprintf('%s%s', $targets[0]->getName(), preg_match('/\[\]$/', $typeString) ? '[]' : '')]
                        );
                    } else {
                        $parts = array_merge($currentNamespace, explode('\\', $typeString));
                    }
                }
            }
        }
        $nullable = false;
        if (count($parts) === 0) { // docCommentから取得できない時には、$typeを解析する。
            if ($type instanceof NullableType) {
                $type = $type->type;
                $nullable = true;
            }
            if ($type instanceof Identifier) {
                $parts[] = $type->name;
            } else if ($type instanceof FullyQualified) {
                $parts = $type->getParts();
            } else if ($type instanceof Name) {
                $typeParts = $type->getParts();
                // usesを検索して適切なnamespaceを探す必要がある。
                $targets = array_values(array_filter($this->uses, function (PhpType $t) use ($typeParts) {
                    $name = end($typeParts);
                    return $name === $t->getName();
                }));
                if (count($targets) > 0) {
                    $parts = array_merge($targets[0]->getNamespace(), [end($typeParts)]);
                } else {
                    $parts = array_merge($currentNamespace, $type->getParts());
                }
            }
        }
        /** @var list<string> $parts */
        $typeName = array_pop($parts);

        return new PhpType(
            namespace: $parts,
            meta: empty($type) ? '' : $type->getType(),
            name: $typeName ?? '',
            alias: null,
            nullable: $nullable
        );
    }

    /**
     * @return PhpType[] types
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function getName(): string
    {
        $types = [];
        foreach ($this->types as $type) {
            $types[] = sprintf('%s%s', $type->getNullable() ? '?' : '', $type->getName());
        }
        return implode('|', $types);
    }
}
