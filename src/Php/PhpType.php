<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

class PhpType {
    public string $name;
    public string $meta;
    public array $namespace;
    public string $alias;

    public function __construct(array $namespace, string $meta, string $name, $alias = null) {
        $this->namespace = $namespace;
        $this->meta = $meta;
        $this->name = $name;
        $this->alias = is_object($alias) ? $alias->name : '';
    }

    public function equals(PhpType $other): bool {
        if (str_replace('[]', '', $this->name) !== str_replace('[]', '', $other->name)) {
            return false;
        }
        if ($this->namespace !== $other->namespace) {
//            var_dump('---' . $this->name);
//            var_dump(json_encode($this->namespace));
//            var_dump(json_encode($other->namespace));
            return false;
        }
        return true;
    }
}
