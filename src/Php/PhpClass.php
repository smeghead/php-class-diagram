<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\ {
    Stmt,
};
use PhpParser\Node\Stmt\ {
    Namespace_,
    ClassLike,
    ClassMethod,
    Property,
    GroupUse,
};

abstract class PhpClass {
    protected string $filename;
    protected Stmt $syntax;

    public function __construct(string $filename, Stmt $syntax) {
        $this->filename = $filename;
        $this->syntax = $syntax;
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
        if ($this->syntax instanceOf ClassLike) {
            return $type_parts;
        } else if ($this->syntax instanceOf Namespace_) {
            $prefix = array_merge($this->syntax->name->parts, $type_parts);
            foreach ($this->syntax->stmts as $stmt) {
                if ($stmt instanceOf GroupUse) {
                    foreach ($stmt->uses as $u) {
                        $parts = $u->name->parts;
                        $end = array_pop($parts);
                        if ($end === $type) {
                            return array_merge($prefix, $parts);
                        }
                    }
                } else if ($stmt instanceOf UseUse) {
                    $parts = $stmt->name->parts;
                    $end = array_pop($parts);
                    if ($end === $type) {
                        return array_merge($prefix, $parts);
                    }
                }
            }
        }
        return [];
    }

    abstract public function getMethods(): array;

    protected function getMethodInfo(ClassMethod $method): PhpMethod {
        return new PhpMethod($method);
    }

    abstract public function getExtends(): array;
}
