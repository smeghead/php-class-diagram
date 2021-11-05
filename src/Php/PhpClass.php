<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\ {
    Identifier,
    Name,
    Stmt,
};
use PhpParser\Node\Stmt\ {
    Namespace_,
    ClassLike,
    Property,
    GroupUse,
};
use Smeghead\PhpClassDiagram\Php\PhpType;

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
            if ( ! $p instanceOf Property) {
                continue;
            }
            if ($p->type instanceOf Identifier) {
                $parts = [$p->type->name];
            } else if ($p->type instanceOf Name) {
                $parts = $p->type->parts;
            } else {
                $parts = []; //型なし
            }
            $namespace = [];
            $typeName = '';
            if (count($parts) > 0) {
                $namespace = $this->findNamespaceByTypeParts($parts);
                $typeName = end($parts);
            }
            $props[] = (object)[
                'name' => $p->props[0]->name->toString(),
                'type' => new PhpType($namespace, $typeName),
            ];
        }
        return $props;
    }

    /**
     * @return Property[] プロパティ一覧
     */
    abstract public function getPropertiesFromSyntax(): array;


    protected function findNamespaceByTypeParts(array $type_parts): array {
        $type = array_pop($type_parts);
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
}
