<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\{
    Namespace_,
    ClassLike,
    ClassMethod,
    Enum_,
    EnumCase,
    Property,
    GroupUse,
    Use_,
};
use Smeghead\PhpClassDiagram\Php\Doc\PhpDocComment;
use Smeghead\PhpClassDiagram\Php\Finders\FindConstructerProperties;
use Smeghead\PhpClassDiagram\Php\Finders\FindUsePhpTypes;

final class PhpClass
{
    /** @var list<string> directory parts */
    private array $dirs;
    private ClassLike $syntax;
    /** @var list<Node> */
    private array $full;

    /**
     * @param list<Node> $full
     */
    public function __construct(string $filename, ClassLike $syntax, array $full)
    {
        $relativePath = dirname($filename);
        if ($relativePath === '.') {
            $dirs = [];
        } else {
            /** @var list<string> $dirs */
            $dirs = (array)preg_split('/[\\\\\/]/', $relativePath);
        }
        $this->dirs = $dirs;
        $this->syntax = $syntax;
        $this->full = $full;
    }

    /**
     * @return string[] parts of the name.
     */
    public function getNamespace(): array
    {
        foreach ($this->full as $stmt) {
            if ($stmt instanceof Namespace_) {
                return $stmt->name->getParts();
            }
        }
        return [];
    }

    /**
     * return logical name.
     * @return string logical name.
     */
    public function getLogicalName(): string
    {
        $type = $this->getClassType();
        $parts = $this->dirs;
        $parts[] = $type->getName();
        return implode('.', $parts);
    }

    /**
     * return className alias in class-diagram.
     * @return string className alias
     */
    public function getClassNameAlias(): string
    {
        return str_replace(['-', '.', '[', ']'], ['', '_', '_', '_'], $this->getLogicalName());
    }

    public function getClassType(): PhpType
    {
        $namespace = [];
        foreach ($this->full as $stmt) {
            if ($stmt instanceof Namespace_) {
                $namespace = $stmt->name->getParts();
                break;
            }
        }
        return new PhpType($namespace, $this->syntax->getType(), $this->syntax->name->name);
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    public function getProperties(): array
    {
        $properties = $this->getPropertiesFromSyntax();
        $props = [];
        foreach ($properties as $p) {
            $props[] = PhpProperty::buildByProperty($p, $this);
        }

        $finder = new FindConstructerProperties($this->syntax);
        foreach ($finder->getProperties() as $param) {
            $props[] = PhpProperty::buildByParam($param, $finder->getConstructer(), $this);
        }
        return $props;
    }

    /**
     * @return Property[] プロパティ一覧
     */
    private function getPropertiesFromSyntax(): array
    {
        return $this->syntax->getProperties();
    }

    /**
     * @return PhpType[] use一覧
     */
    public function getUses(): array
    {
        return $this->getUsesRec($this->full);
    }

    /**
     * @param list<Node> $stmts Stmts
     * @param PhpType[] $uses
     * @return PhpType[]
     */
    private function getUsesRec($stmts, array $uses = []): array
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof GroupUse) {
                $prefix = $stmt->prefix->getParts();
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->getParts();
                    $name = array_pop($parts);
                    $uses[] = new PhpType(array_merge($prefix, $parts), '', $name, $u->alias);
                }
            } else if ($stmt instanceof Use_) {
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->getParts();
                    $name = array_pop($parts);
                    $uses[] = new PhpType($parts, '', $name, $u->alias);
                }
            } else if ($stmt instanceof Namespace_) {
                $uses = array_merge($uses, $this->getUsesRec($stmt->stmts, $uses));
            }
        }
        return $uses;
    }

    /** @return PhpMethod[] メソッド一覧 */
    public function getMethods(): array
    {
        $methods = [];
        foreach ($this->syntax->stmts as $stmt) {
            if ($stmt instanceof ClassMethod) {
                $methods[] = $this->getMethodInfo($stmt);
            }
        }
        return $methods;
    }

    private function getMethodInfo(ClassMethod $method): PhpMethod
    {
        return new PhpMethod($method, $this);
    }

    /**
     * @return PhpType[] 継承元と実装元型一覧
     */
    public function getExtends(): array
    {
        $extends = [];
        if (property_exists($this->syntax, 'extends')) {
            $extend = $this->syntax->{'extends'};
            if ($extend instanceof FullyQualified) {
                $parts = $extend->getParts();
                $extends[] = new PhpType(
                    array_slice($parts, 0, count($parts) - 1),
                    '',
                    (string)end($parts)
                );
            }
        }
        
        if (property_exists($this->syntax, 'implements')) {
            foreach ($this->syntax->{'implements'} as $implement) {
                if ($implement instanceof FullyQualified) {
                    $parts = $implement->getParts();
                    $extends[] = new PhpType(
                        array_slice($parts, 0, count($parts) - 1),
                        '',
                        (string)end($parts)
                    );
                }
            }
        }
        return $extends;
    }

    /**
     * @return PhpEnumCase[] list of enum options.
     */
    public function getEnumCases(): array
    {
        if (!$this->syntax instanceof Enum_) {
            return [];
        }
        $cases = [];
        foreach ($this->syntax->stmts as $stmt) {
            if ($stmt instanceof EnumCase) {
                $cases[] = new PhpEnumCase($stmt);
            }
        }
        return $cases;
    }

    public function getDescription(): string
    {
        return (new PhpDocComment($this->syntax))
            ->getDescription();
    }

    /**
     * @return list<PhpType>
     */
    public function getUsingTypes(): array
    {
        $finder = new FindUsePhpTypes($this->syntax);
        return array_map(
            function(FullyQualified $x) {
                $parts = $x->getParts();
                return new PhpType(
                    array_slice($parts, 0, count($parts) - 1), '', (string)end($parts)
                );
            },
            $finder->collectTypes()
        );
    }
}
