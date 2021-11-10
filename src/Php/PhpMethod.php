<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt\ {
    ClassMethod,
};

class PhpMethod {
    public string $name;
    /** @var PhpMethodParameter[] パラメータ一覧 */
    public array $params;
    public PhpAccessModifier $accessModifier;

    public function __construct(ClassMethod $method) {
        $params = array_map(function($x){
            $type = '';
            if ( ! empty($x->type)) {
                $type = $x->type->toString();
            }
            return new PhpMethodParameter($x->var->name, new PhpType([], '', $type)); //TODO パッケージとmetaを仮とする。
        }, $method->getParams());
        $this->name = $method->name->toString();
        $this->params = $params;
        $this->accessModifier = new PhpAccessModifier($method);
    }
}
