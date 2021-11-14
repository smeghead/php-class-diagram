<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ {
    ClassLike,
    ClassMethod,
};

class PhpClassNamespace extends PhpClass {

    public function __construct(string $filename, Stmt $syntax) {
        parent::__construct($filename, $syntax);
        //check includeing class. if not include any class, throw exception.
        $this->findClassLike();
    }

    public function getClassType(): PhpType {
        $c = $this->findClassLike();
        return new PhpType($this->syntax->name->parts, $c->getType(), $c->name->name);
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    protected function getPropertiesFromSyntax(): array {
        return $this->findClassLike()->getProperties();
    }

    public function getMethods(): array {
        $syntax = $this->findClassLike();
        $methods = [];
        foreach ($syntax->stmts as $stmt) {
            if ($stmt instanceOf ClassMethod) {
                $methods[] = $this->getMethodInfo($stmt);
            }
        }
        return $methods;
    }

    private function findClassLike(): ClassLike {
        foreach ($this->syntax->stmts as $c) {
            if ($c instanceOf ClassLike) {
                return $c;
            }
        }
        throw new \Exception('failed to find class.');
    }

    public function getExtends(): array {
        $namespace = $this->syntax->name->parts;
        $syntax = $this->findClassLike();
        $extends = [];
        if ( ! empty($syntax->extends)) {
            $Name = $syntax->extends;
            if (is_array($syntax->extends)) {
                $Name = $syntax->extends[0];
            } 
            $parts = $Name->parts;
            $name = array_pop($parts);
            $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Class', $name);
        }
        if ( ! empty($syntax->implements)) {
            foreach ($syntax->implements as $i) {
                $parts = $i->parts;
                $name = array_pop($parts);
                $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Interface', $name);
            }
        }
        return $extends;
    }
}
