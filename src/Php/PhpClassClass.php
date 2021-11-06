<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ClassMethod;

use Smeghead\PhpClassDiagram\Php\PhpType;

class PhpClassClass extends PhpClass {

    public function getClassType(): PhpType {
        return new PhpType([], $this->syntax->getType(), $this->syntax->name->name);
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    public function getPropertiesFromSyntax(): array {
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
}
