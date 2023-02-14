<?php declare(strict_types=1);

use Smeghead\PhpClassDiagram\Php\ {
  PhpType,
  PhpMethod,
  PhpAccessModifier,
  PhpMethodParameter,
    PhpTypeExpression,
};

require_once(__DIR__ . '/PhpAccessModifierDummy.php');

class PhpMethodDummy extends PhpMethod {
    // public string $name;
    // public PhpType $type;
    // /** @var PhpMethodParameter[] パラメータ一覧 */
    // public array $params;
    // public PhpAccessModifier $accessModifier;

    public function __construct(\stdClass $method) {
        $params = array_map(function($x){
            return new PhpMethodParameter($x->name, PhpTypeExpression::buildByPhpType(new PhpType([], '', $x->type->name)));
        }, $method->params);
        $this->name = $method->name;
        $this->type = new PhpType($method->type->namespace, 'Stmt_Class', $method->type->name);
        $this->params = $params;
        $this->accessModifier = new PhpAccessModifierDummy($method->modifier);
    }
}
