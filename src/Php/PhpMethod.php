<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\ {
    NullableType,
    Name,
};
use PhpParser\Node\Stmt\ {
    ClassMethod,
};

class PhpMethod {
    public string $name;
    public PhpType $type;
    /** @var PhpMethodParameter[] パラメータ一覧 */
    public array $params;
    public PhpAccessModifier $accessModifier;

    public function __construct(ClassMethod $method, PhpClass $class) {
        $params = array_map(function($x) use ($class){
            $type = $class->findTypeByTypeParts($x, 'type');
            return new PhpMethodParameter($x->var->name, $type);
        }, $method->getParams());
        $this->name = $method->name->toString();
        $this->type = $this->getTypeFromMethod($method, $class);
        $this->params = $params;
        $this->accessModifier = new PhpAccessModifier($method);
    }

    private function getTypeFromMethod(ClassMethod $method, PhpClass $class): PhpType {
        return $class->findTypeByTypeParts($method, 'returnType', 'return');
    }
}
