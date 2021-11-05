<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\ {
    Namespace_,
    ClassLike,
};
use Smeghead\PhpClassDiagram\Php\ {
    PhpClass,
    PhpClassClass,
    PhpClassNamespace,
};

class PhpReflection {
    private string $filename;
    private PhpClass $class;
    public function __construct(string $filename) {
        $this->filename = $filename;
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

    private function getClass($ast): PhpClass {
        if (count($ast) === 0) {
            return null;
        }
        foreach ($ast as $element) {
            if ($element instanceOf ClassLike) {
                return new PhpClassClass($element);
            } else if ($element instanceOf Namespace_) {
                return new PhpClassNamespace($element);
            }
        }
        // クラスが含まれていないファイル
        var_dump($ast);die('');
    }

    public function getInfo(): \stdClass {
        $data = (object)[
            'type' => $this->class->getClassType(),
            'properties' => $this->class->getProperties(),
        ];
        return $data;
    }
}
