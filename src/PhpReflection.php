<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\Node\ {
    Identifier,
    Name,
};
use PhpParser\Node\Stmt\ {
    ClassLike,
    Property,
};

class PhpReflection {
    private string $filename;
    private ClassLike $class;
    public function __construct(string $filename) {
        $this->filename = $filename;
        // クラス名に使える文字 https://www.php.net/manual/ja/language.oop5.basic.php
        if ( ! preg_match('/[\/\\\\]([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)\.php/i', $this->filename, $matches)) {
            throw new Exception('invalid filename.');
        }
        $className = $matches[1];
        $code = file_get_contents($this->filename);

        // TODO バージョンをオプション指定できるようにする。
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            throw new Exception("Parse error: {$error->getMessage()} file: {$this->filename}\n");
        }

        $this->class = $this->getClass($ast);
    }

    private function getClass($ast): ?ClassLike {
        if (count($ast) === 0) {
            return null;
        }
        foreach ($ast as $element) {
            if ($element instanceOf ClassLike) {
                return $element;
            }
        }
        // クラスが含まれていないファイル
        var_dump($ast);die();
    }

    private function getClassname(): string {
        return $this->class->name->name;
    }

    private function getProperties(): array {
        $properties = $this->class->stmts;

        $props = [];
        foreach ($properties as $p) {
            if ( ! $p instanceOf Property) {
                continue;
            }
            if ($p->type instanceOf Identifier) {
                $type = $p->type->name;
            } else if ($p->type instanceOf Name) {
                $type = $p->type->parts[0];
            } else {
                $type = ''; //型なし
            }
            $props[] = (object)[
                'name' => $p->props[0]->name->name,
                'type' => $type,
            ];
        }
        return $props;
    }

    public function getInfo(): \stdClass {
        $data = (object)[
            'name' => $this->getClassname(),
            'namespace' => '', //TODO namespaceの取得
            'properties' => $this->getProperties(),
        ];
        return $data;
    }
}
