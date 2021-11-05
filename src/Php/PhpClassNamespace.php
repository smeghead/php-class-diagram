<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ClassLike;

use Smeghead\PhpClassDiagram\Php\PhpType;

class PhpClassNamespace extends PhpClass {

    public function getClassType(): PhpType {
        foreach ($this->syntax->stmts as $stmt) {
            if ($stmt instanceOf ClassLike) {
                return new PhpType($this->syntax->name->parts, $stmt->name->name);
            }
        }
        throw new \Exception('not found class. ' . $this->filename);
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    public function getPropertiesFromSyntax(): array {
        $syntax = null;
        foreach ($this->syntax->stmts as $c) {
            if ($c instanceOf ClassLike) {
                $syntax = $c;
                break;
            }
        }
        if ($syntax === null) {
            throw new \Exception('failed to find class.');
        }
        return $syntax->getProperties();
    }
}
