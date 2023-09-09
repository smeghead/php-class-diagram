<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

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
    /** @var string[] directory parts */
    private array $dirs;
    private ClassLike $syntax;
    /** @var \PhpParser\Node[] */
    private array $full;

    /**
     * @param \PhpParser\Node[] $full
     */
    public function __construct(string $filename, ClassLike $syntax, array $full)
    {
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

    /**
     * @return string[] parts of the name.
     */
    public function getNamespace(): array
    {
        foreach ($this->full as $stmt) {
            if ($stmt instanceof Namespace_) {
                return $stmt->name->parts;
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
        return str_replace(['.', '[', ']'], '_', $this->getLogicalName());
    }

    public function getClassType(): PhpType
    {
        $namespace = [];
        foreach ($this->full as $stmt) {
            if ($stmt instanceof Namespace_) {
                $namespace = $stmt->name->parts;
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
     * @param \PhpParser\Node[] $stmts Stmts
     * @param PhpType[] $uses
     * @return PhpType[]
     */
    private function getUsesRec($stmts, array $uses = []): array
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof GroupUse) {
                $prefix = $stmt->prefix->parts;
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->parts;
                    $name = array_pop($parts);
                    $uses[] = new PhpType(array_merge($prefix, $parts), '', $name, $u->alias);
                }
            } else if ($stmt instanceof Use_) {
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->parts;
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
                $extends[] = new PhpType(
                    array_slice($extend->parts, 0, count($extend->parts) - 1),
                    '',
                    end($extend->parts)
                );
            }
        }
        
        if (property_exists($this->syntax, 'implements')) {
            foreach ($this->syntax->{'implements'} as $implement) {
                if ($implement instanceof FullyQualified) {
                    $extends[] = new PhpType(
                        array_slice($implement->parts, 0, count($implement->parts) - 1),
                        '',
                        end($implement->parts)
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
        $doc = new PhpDocComment($this->syntax);
        return $doc->getDescription();
    }

    /**
     * @return PhpType[] using types.
     */
    public function getUsingTypes(): array
    {
        $finder = new FindUsePhpTypes($this->syntax);
        return array_map(fn(FullyQualified $x) => new PhpType(array_slice($x->parts, 0, count($x->parts) - 1), '', end($x->parts)), $finder->collectTypes());
    }
}
