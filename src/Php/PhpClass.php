<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\NodeAbstract;
use PhpParser\Node\ {
    NullableType,
    Identifier,
    Name,
};
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ {
    Namespace_,
    ClassLike,
    ClassMethod,
    Property,
    GroupUse,
    Use_,
};

abstract class PhpClass {
    /** @var string[] directory parts */
    protected array $dirs;
    protected Stmt $syntax;

    public function __construct(string $filename, Stmt $syntax) {
        $relativePath = dirname($filename);
        if ($relativePath === '.') {
            $dirs = [];
        } else {
            $dirs = preg_split('/[\\\\\/]/', $relativePath);
        }
        $this->dirs = $dirs;
        $this->syntax = $syntax;
    }

    /**
     * return logical name.
     * @return string logical name.
     */
    public function getLogicalName(): string {
        $type = $this->getClassType();
        $parts = $this->dirs; 
        $parts[] = $type->name;
        return implode('.', $parts);
    }

    abstract public function getClassType(): PhpType;

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    public function getProperties(): array {
        $properties = $this->getPropertiesFromSyntax();
        $props = [];
        foreach ($properties as $p) {
            $props[] = new PhpProperty($p, $this);
        }
        return $props;
    }

    /**
     * @return Property[] プロパティ一覧
     */
    abstract protected function getPropertiesFromSyntax(): array;

    /**
     * @return PhpType[] use一覧
     */
    abstract public function getUses(): array;

    /**
     * クラス名が、どのnamespaceに属しているかを判定する。
     *
     * * useしているクラスに目的のクラスがあるかを探す
     * * 自身のクラス名が目的のクラスかどうか   ... (不要かもしれない。暗黙の参照と統合可能
     * * 暗黙の参照として、自身のnamespaceを返却する
     */
    protected function findNamespaceByTypeParts(array $type_parts): array {
        $type = str_replace('[]', '', array_pop($type_parts));
        $primitives = ['string', 'bool', 'boolean', 'int', 'integer', 'float', 'double', 'array', 'object', 'resource'];
        if (in_array($type, $primitives)) {
            return [];
        }
        foreach ($this->getUses() as $u) {
            if ($u->name === $type) {
                return $u->namespace;
            }
        }
        // 探したいクラスが、自身の型だった場合
        $t = $this->getClassType();
        if ($t->name === $type) {
            return $t->namespace;
        }

        // 暗黙的な参照と見做す
        if ($this->syntax instanceOf Namespace_) {
            return $this->syntax->name->parts;
        } else {
            return [];
        }
    }

    /**
     * php-parserの解析結果の情報から、指定のクラスの型情報を取得します。
     * クラスのuseの状態から、namespaceを解決したPhpTypeを返却します。
     *
     * @param NodeAbstract ClassMethod または Property または Param が渡されることを想定
     */
    public function findTypeByTypeParts(NodeAbstract $stmt, string $property, string $docAttribute = ''): PhpType {
        $docComment = $stmt->getDocComment();
        $doc = '';
        if ( ! empty($docComment)) {
            $doc =  $docComment->getText();
        }
        $parts = [];
        $type = $stmt->{$property};
        if ($type instanceOf NullableType) {
            $type = $type->type;
        }
        if ( ! empty($doc) && ! empty($docAttribute)) {
            // @return に定義された型情報を取得する。
            if (preg_match(sprintf('/@%s\s+(\S+)(\b|\s).*/', $docAttribute), $doc, $matches)) {
                $parts[] = $matches[1];
            }
        } else if ($type instanceOf Identifier) {
            $parts[] = $type->name;
        } else if ($type instanceOf FullyQualified) {
            return new PhpType(
                array_slice($type->parts, 0, count($type->parts) - 1),
                '',
                end($type->parts));
        } else if ($type instanceOf Name) {
            $parts = $type->parts;
        }
        $namespace = [];
        if (count($parts) > 0) {
            $namespace = $this->findNamespaceByTypeParts($parts);
            $typeName = array_pop($parts);
        }
        return new PhpType($namespace, $stmt->getType(), $typeName ?? '');
    }

    /** @return PhpMethod[] メソッド一覧 */
    abstract public function getMethods(): array;

    protected function getMethodInfo(ClassMethod $method): PhpMethod {
        return new PhpMethod($method, $this);
    }

    /**
     * @return PhpType[] 継承元と実装元型一覧
     */
    abstract public function getExtends(): array;
}
