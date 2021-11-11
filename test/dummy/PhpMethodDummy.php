<?php declare(strict_types=1);

use Smeghead\PhpClassDiagram\Php\ {
  PhpType,
  PhpMethod,
  PhpAccessModifier,
  PhpMethodParameter,
};

require_once(__DIR__ . '/PhpAccessModifierDummy.php');

class PhpMethodDummy extends PhpMethod {
    public string $name;
    /** @var PhpMethodParameter[] パラメータ一覧 */
    public array $params;
    public PhpAccessModifier $accessModifier;

    public function __construct(\stdClass $method) {
        $params = array_map(function($x){
            return new PhpMethodParameter($x->name, new PhpType([], '', $x->type->name));
        }, $method->params);
        $this->name = $method->name;
        $this->params = $params;
        $this->accessModifier = new PhpAccessModifierDummy($method->modifier);
    }
}
