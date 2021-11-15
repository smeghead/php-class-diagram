<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

class PhpType {
    public string $name;
    public string $meta;
    public array $namespace;
    public string $alias;

    public function __construct(array $namespace, string $meta, string $name, string $alias = '') {
        $this->namespace = $namespace;
        $this->meta = $meta;
        $this->name = $name;
        $this->alias = $alias;
    }

    public function equals(PhpType $other): bool {
        if ($this->namespace !== $other->namespace) {
            return false;
        }
        if (str_replace('[]', '', $this->name) !== str_replace('[]', '', $other->name)) {
            return false;
        }
        return true;
    }
}
