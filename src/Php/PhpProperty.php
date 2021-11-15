<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\ {
    Identifier,
    Name,
};
use PhpParser\Node\Stmt\ {
    Property,
};
use Smeghead\PhpClassDiagram\Php\ {
    PhpAccessModifier,
};

class PhpProperty {
    public string $name;
    public PhpType $type;
    public PhpAccessModifier $accessModifier;

    public function __construct(Property $p, PhpClass $class) {
        $docComment = $p->getDocComment();
        $doc = '';
        if ( ! empty($docComment)) {
            $doc =  $docComment->getText();
        }
        $t = $class->getClassType();
        $parts = [];
        if ( ! empty($doc)) {
            // @var に定義された型情報を取得する。
            if (preg_match('/@var\s+(\S+)(\b|\s).*/', $doc, $matches)) {
                $parts = $t->namespace;
                $parts[] = $matches[1];
            }
        } else if ($p->type instanceOf Identifier) {
            $parts = $t->namespace;
            $parts[] = $p->type->name;
        } else if ($p->type instanceOf Name) {
            $parts = array_merge($t->namespace, $p->type->parts);
        }
        if (count($parts) > 0) {
            $typeName = array_pop($parts);
        }
        $this->name = $p->props[0]->name->toString();
        $this->type = new PhpType($parts, '', $typeName ?? '');
        $this->accessModifier = new PhpAccessModifier($p);
    }
}
