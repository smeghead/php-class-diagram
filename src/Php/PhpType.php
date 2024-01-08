<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

final class PhpType
{
    private string $name;
    private string $meta;
    /** @var string[] */
    private array $namespace;
    private string $alias;
    private bool $nullable;

    /**
     * @param string[] $namespace Namespace
     * @param \PhpParser\Node\Identifier|null $alias Alias
     */
    public function __construct(array $namespace, string $meta, string $name, $alias = null, bool $nullable = false)
    {
        $this->namespace = $namespace;
        $this->meta = $meta;
        $this->name = $name;
        $this->alias = is_object($alias) ? $alias->name : '';
        $this->nullable = $nullable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMeta(): string
    {
        return $this->meta;
    }

    public function getMetaName(): string
    {
        switch ($this->meta) {
            case 'Stmt_Interface':
                return 'interface';
            case 'Stmt_Enum':
                return 'enum';
            case 'Stmt_Trait':
                return 'class';
            default:
                return 'class';
        }
    }

    /**
     * @return string[] namespace
     */
    public function getNamespace(): array
    {
        return $this->namespace;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Determine whether they are the same class.
     * 
     * Since it is used to determine dependency arrows,
     * array representations of the same class are also determined to be the same class.
     */
    public function equals(PhpType $other): bool
    {
        if ($this->namespace !== $other->namespace) {
            return false;
        }
        // ex. Product or Product[]
        if (str_replace('[]', '', $this->name) === str_replace('[]', '', $other->name)) {
            return true;
        }
        // ex. array<Product>
        if (preg_match('/array<([^,>]+)>/', $other->name, $matches)) {
            if ($this->name === $matches[1]) {
                return true;
            }
        }
        // ex. array<int, Product>
        if (preg_match('/array<[^>]+,\s*([^>]+)>/', $other->name, $matches)) {
            if ($this->name === $matches[1]) {
                return true;
            }
        }
        return false;
    }
}
