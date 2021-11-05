<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use Smeghead\PhpClassDiagram\Php\PhpType;

class PhpClassClass extends PhpClass {

    public function getClassType(): PhpType {
        return new PhpType([], $this->syntax->name->name);
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    public function getPropertiesFromSyntax(): array {
        return $this->syntax->getProperties();
    }
}
