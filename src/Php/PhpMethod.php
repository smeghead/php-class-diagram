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
    protected string $name;
    protected PhpType $type;
    /** @var PhpMethodParameter[] パラメータ一覧 */
    protected array $params;
    protected PhpAccessModifier $accessModifier;

    public function __construct(ClassMethod $method, PhpClass $class) {
        $docString = '';
        $doc = $method->getDocComment();
        if ( ! empty($doc)) {
            $docString = $doc->getText();
        }
        $params = array_map(function($x) use ($class, $docString){
            // $type = $class->findTypeByTypeParts($x, 'type');
            $type = PhpTypeExpression::buildByMethodParam($x, $class->getNamespace(), $docString, $x->var->name, $class->getUses());
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

    public function getName(): string {
        return $this->name;
    }

    public function getType(): PhpType {
        return $this->type;
    }

    /**
     * @return PhpMethodParameter[]
     */
    public function getParams(): array {
        return $this->params;
    }

    public function getAccessModifier(): PhpAccessModifier {
        return $this->accessModifier;
    }
}
