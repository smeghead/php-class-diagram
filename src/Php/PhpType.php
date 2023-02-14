<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

class PhpType {
    private string $name;
    private string $meta;
    private array $namespace;
    private string $alias;
    private bool $nullable;

    public function __construct(array $namespace, string $meta, string $name, $alias = null, bool $nullable = false) {
        $this->namespace = $namespace;
        $this->meta = $meta;
        $this->name = $name;
        $this->alias = is_object($alias) ? $alias->name : '';
        $this->nullable = $nullable;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getMeta(): string {
        return $this->meta;
    }

    /**
     * @return string[] namespace
     */
    public function getNamespace(): array {
        return $this->namespace;
    }

    public function getAlias(): string {
        return $this->alias;
    }

    public function getNullable(): bool {
        return $this->nullable;
    }
    
    public function equals(PhpType $other): bool {
        if (str_replace('[]', '', $this->name) !== str_replace('[]', '', $other->name)) {
            return false;
        }
        if ($this->namespace !== $other->namespace) {
            return false;
        }
        return true;
    }
}
