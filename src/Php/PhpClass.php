<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

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

    public function findNamespaceByTypeParts(array $type_parts): array {
        $type = str_replace('[]', '', array_pop($type_parts));
        $primitives = ['string', 'bool', 'boolean', 'int', 'integer', 'float', 'double', 'array', 'object', 'resource'];
        if (in_array($type, $primitives)) {
            return [];
        }
        if ($this->syntax instanceOf ClassLike) {
            return $type_parts;
        } else if ($this->syntax instanceOf Namespace_) {
            foreach ($this->syntax->stmts as $stmt) {
                if ($stmt instanceOf GroupUse) {
                    $prefix = $stmt->prefix->parts;
                    foreach ($stmt->uses as $u) {
                        $parts = $u->name->parts;
                        $end = array_pop($parts);
                        if ($end === $type) {
                            return array_merge($prefix, $parts);
                        }
                    }
                } else if ($stmt instanceOf Use_) {
                    foreach ($stmt->uses as $u) {
                        $parts = $u->name->parts;
                        $end = array_pop($parts);
                        if ($end === $type) {
                            return $parts;
                        }
                    }
                }
            }
        }
        // 探したいクラスが、自身の型だった場合
        $t = $this->getClassType();
        if ($type === $t->name) {
            return $t->namespace;
        }

        // 暗黙的な参照と見做す
        return $this->syntax->name->parts;
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
