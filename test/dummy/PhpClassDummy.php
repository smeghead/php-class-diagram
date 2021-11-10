<?php declare(strict_types=1);

use PhpParser\Node\ {
    Stmt,
};
use Smeghead\PhpClassDiagram\Php\ {
    PhpClass,
    PhpType,
    PhpProperty,
};
require_once(__DIR__ . '/PhpMethodDummy.php');

/**
 * Dummy Class for tests.
 */
class PhpClassDummy extends PhpClass {
    public function __construct(string $filename, Stmt $syntax = null) {
        $this->data = json_decode($filename);
    }

    public function getClassType(): PhpType {
        return new PhpType($this->data->type->namespace, $this->data->type->meta, $this->data->type->name);
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    public function getProperties(): array {
        $props = [];
        foreach ($this->data->properties as $p) {
            $props[] = new PhpProperty($p->name, new PhpType($p->type->namespace, '', $p->type->name));
        }
        return $props;
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    protected function getPropertiesFromSyntax(): array {
        throw new Exception('not implement.');
    }

    public function getMethods(): array {
        $methods = [];
        foreach ($this->data->methods as $m) {
            $methods[] = new PhpMethodDummy($m);
        }
        return $methods;
    }

    public function getExtends(): array {
        $namespace = [];
        $extends = [];
        if ( ! empty($this->data->extends)) {
            foreach ($this->data->extends as $extend) {
                $extends[] = new PhpType($extend->namespace, $extend->meta, $extend->name);
            }
        }
        return $extends;
    }
}
