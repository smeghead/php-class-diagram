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
        $t = $class->getClassType();
        $params = array_map(function($x) use ($t){
            $namespace = $t->namespace;
            $type = '';
            if ( ! empty($x->type)) {
                $t = $x->type;
                if ($t instanceOf NullableType) {
                    $t = $t->type;
                }
                if ($t instanceOf Name) {
                    $namespace = array_merge($namespace, $t->parts);
                }
                $type = $t->toString();
            }
            array_pop($namespace);
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
        $t = $class->getClassType();
        $parts = [];
        $returnType = $method->returnType;
        if ($returnType instanceOf NullableType) {
            $returnType = $returnType->type;
        }
        if ( ! empty($doc)) {
            // @return に定義された型情報を取得する。
            if (preg_match('/@return\s+(\S+)(\b|\s).*/', $doc, $matches)) {
                $parts = $t->namespace;
                $parts[] = $matches[1];
            }
        } else if ($returnType instanceOf Identifier) {
            $parts = $t->namespace;
            $parts[] = $returnType->name;
        } else if ($returnType instanceOf Name) {
            $parts = array_merge($t->namespace, $returnType->parts);
        }
        if (count($parts) > 0) {
            $typeName = array_pop($parts);
        }
        return new PhpType($parts, $method->getType(), $typeName ?? '');
    }
}
