<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\ {
    NullableType,
    Identifier,
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
        $params = array_map(function($x){
            $namespace = [];
            $type = '';
            if ( ! empty($x->type)) {
                $t = $x->type;
                if ($x->type instanceOf NullableType) {
                    $t = $type->type;
                }
                if ($t instanceOf Name) {
                    $namespace = $t->parts;
                }
                $type = $t->toString();
            }
            return new PhpMethodParameter($x->var->name, new PhpType($namespace, '', $type)); //TODO metaを仮とする。
        }, $method->getParams());
        $this->name = $method->name->toString();
        $this->type = $this->getTypeFromMethod($method, $class);
        $this->params = $params;
        $this->accessModifier = new PhpAccessModifier($method);
    }

    private function getTypeFromMethod(ClassMethod $method, PhpClass $class): PhpType {
        $docComment = $method->getDocComment();
        $doc = '';
        if ( ! empty($docComment)) {
            $doc =  $docComment->getText();
        }
        $parts = [];
        if ( ! empty($doc)) {
            // @return に定義された型情報を取得する。
            if (preg_match('/@return\s+(\S+)(\b|\s).*/', $doc, $matches)) {
                $parts = [$matches[1]];
            }
        } else if ($method->returnType instanceOf Identifier) {
            $parts = [$method->returnType->name];
        } else if ($method->returnType instanceOf Name) {
            $parts = $method->returnType->parts;
        } else {
            $parts = []; //型なし
        }
        $typeName = '';
        $namespace = [];
        if (count($parts) > 0) {
            $namespace = $class->findNamespaceByTypeParts($parts);
            $typeName = end($parts);
        }
        return new PhpType($namespace, $method->getType(), $typeName);
    }
}
