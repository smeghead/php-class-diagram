<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\NodeAbstract;
use PhpParser\Node\ {
    NullableType,
    Identifier,
    Name,
    UnionType,
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

class PhpClass {
    /** @var string[] directory parts */
    protected array $dirs;
    protected Stmt $syntax;
    protected array $full;

    public function __construct(string $filename, Stmt $syntax, array $full) {
        $relativePath = dirname($filename);
        if ($relativePath === '.') {
            $dirs = [];
        } else {
            $dirs = preg_split('/[\\\\\/]/', $relativePath);
        }
        $this->dirs = $dirs;
        $this->syntax = $syntax;
        $this->full = $full;
    }

    public function getNamespace(): array {
        foreach($this->full as $stmt) {
            if ($stmt instanceOf Namespace_) {
                return $stmt->name->parts;
            }
        }
        return [];
    }

    /**
     * return logical name.
     * @return string logical name.
     */
    public function getLogicalName(): string {
        $type = $this->getClassType();
        $parts = $this->dirs; 
        $parts[] = $type->getName();
        return implode('.', $parts);
    }

    public function getClassType(): PhpType {
        $namespace = [];
        foreach ($this->full as $stmt) {
            if ($stmt instanceOf Namespace_) {
                $namespace = $stmt->name->parts;
                if ($namespace === null) {
                    var_dump($stmt);
                }
                break;
            }
        }
        return new PhpType($namespace, $this->syntax->getType(), $this->syntax->name->name);
    }

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
    protected function getPropertiesFromSyntax(): array {
        return $this->syntax->getProperties();
    }

    /**
     * @return PhpType[] use一覧
     */
    public function getUses(): array {
        return $this->getUsesRec($this->full);
    }

    private function getUsesRec($stmts, $uses = []) {
        foreach ($stmts as $stmt) {
            if ($stmt instanceOf GroupUse) {
                $prefix = $stmt->prefix->parts;
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->parts;
                    $name = array_pop($parts);
                    $uses[] = new PhpType(array_merge($prefix, $parts), '', $name, $u->alias); 
                }
            } else if ($stmt instanceOf Use_) {
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->parts;
                    $name = array_pop($parts);
                    $uses[] = new PhpType($parts, '', $name, $u->alias); 
                }
            } else if ($stmt instanceOf Namespace_) {
                $uses = array_merge($uses, $this->getUsesRec($stmt->stmts, $uses));
            }
        }
        return $uses;
    }

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
            if ($u->getName() === $type) {
                return $u->getNamespace();
            }
        }
        // 探したいクラスが、自身の型だった場合
        $t = $this->getClassType();
        if ($t->getName() === $type) {
            return $t->getNamespace();
        }

        // 暗黙的な参照と見做す
        return $this->getNamespace();
    }

    /**
     * php-parserの解析結果の情報から、指定のクラスの型情報を取得します。
     * クラスのuseの状態から、namespaceを解決したPhpTypeを返却します。
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
            // @{$docAttribute} に定義された型情報を取得する。
            if (preg_match(sprintf('/@%s\s+(\S+)(\b|\s).*/', $docAttribute), $doc, $matches)) {
                $typeString = $matches[1];
                if (mb_substr($typeString, 0, 1) === '\\') {
                    $parts = explode('\\', mb_substr($typeString, 1));
                    $typeName = array_pop($parts);
                    return new PhpType($parts, '', $typeName);
                }

                $parts = explode('\\', $typeString);
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
        } else if ($type instanceOf UnionType) {

        }
        $namespace = [];
        if (count($parts) > 0) {
            $namespace = $this->findNamespaceByTypeParts($parts);
            $typeName = array_pop($parts);
        }
        return new PhpType($namespace, $stmt->getType(), $typeName ?? '');
    }

    /** @return PhpMethod[] メソッド一覧 */
    public function getMethods(): array {
        $methods = [];
        foreach ($this->syntax->stmts as $stmt) {
            if ($stmt instanceOf ClassMethod) {
                $methods[] = $this->getMethodInfo($stmt);
            }
        }
        return $methods;
    }

    protected function getMethodInfo(ClassMethod $method): PhpMethod {
        return new PhpMethod($method, $this);
    }

    /**
     * @return PhpType[] 継承元と実装元型一覧
     */
    public function getExtends(): array {
        $extends = [];
        if ( ! empty($this->syntax->extends)) {
            $Name = $this->syntax->extends;
            if (is_array($this->syntax->extends)) {
                $Name = $this->syntax->extends[0];
            } 
            if ($Name instanceOf FullyQualified) {
                $extends[] = new PhpType(
                    array_slice($Name->parts, 0, count($Name->parts) - 1),
                    '',
                    end($Name->parts));
            } else {
                $parts = $Name->parts;
                $namespace = [];
                if (count($parts) > 0) {
                    $namespace = $this->findNamespaceByTypeParts($parts);
                    $typeName = array_pop($parts);
                }
                $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Class', $typeName);
            }
        }
        if ( ! empty($this->syntax->implements)) {
            foreach ($this->syntax->implements as $i) {
                $parts = $i->parts;
                $namespace = [];
                if (count($parts) > 0) {
                    $namespace = $this->findNamespaceByTypeParts($parts);
                    $typeName = array_pop($parts);
                }
                $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Interface', $typeName);
            }
        }
        return $extends;
    }
}
