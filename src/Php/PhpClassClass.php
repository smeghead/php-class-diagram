<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ClassMethod;

class PhpClassClass extends PhpClass {

    public function getClassType(): PhpType {
        return new PhpType([], $this->syntax->getType(), $this->syntax->name->name);
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    protected function getPropertiesFromSyntax(): array {
        return $this->syntax->getProperties();
    }

    public function getMethods(): array {
        $methods = [];
        foreach ($this->syntax->stmts as $stmt) {
            if ($stmt instanceOf ClassMethod) {
                $methods[] = $this->getMethodInfo($stmt);
            }
        }
        return $methods;
    }

    /**
     * @return PhpType[] 継承元と実装元型一覧
     */
    public function getExtends(): array {
        $namespace = [];
        $extends = [];
        if ( ! empty($this->syntax->extends)) {
            $Name = $this->syntax->extends;
            if (is_array($this->syntax->extends)) {
                $Name = $this->syntax->extends[0];
            } 
            $parts = $Name->parts;
            $name = array_pop($parts);
            $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Class', $name);
        }
        if ( ! empty($this->syntax->implements)) {
            foreach ($this->syntax->implements as $i) {
                $parts = $i->parts;
                $name = array_pop($parts);
                $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Interface', $name);
            }
        }
        return $extends;
    }
}
