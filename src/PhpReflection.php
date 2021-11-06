<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\ {
    Namespace_,
    ClassLike,
};
use Smeghead\PhpClassDiagram\Options;
use Smeghead\PhpClassDiagram\Php\ {
    PhpClass,
    PhpClassClass,
    PhpClassNamespace,
};

class PhpReflection {
    private string $filename;
    private PhpClass $class;
    public function __construct(string $filename, Options $options) {
        $this->filename = $filename;
        $code = file_get_contents($this->filename);

        $targetVesion = ParserFactory::PREFER_PHP7;
        switch ($options->phpVersion()) {
            case 'php5':
                $targetVesion = ParserFactory::PREFER_PHP5;
                break;
            case 'php7':
                $targetVesion = ParserFactory::PREFER_PHP7;
                break;
            case 'php8':
                $targetVesion = ParserFactory::PREFER_PHP7; // php-parser でまだ php8 がサポートされていない。
                break;
            default:
                throw new \Exception("invalid php version. {$targetVesion}\n");
        }
        $parser = (new ParserFactory)->create($targetVesion);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$this->filename}\n");
        }

        $this->class = $this->getClass($ast);
    }

    private function getClass($ast): PhpClass {
        if (count($ast) === 0) {
            return null;
        }
        foreach ($ast as $element) {
            if ($element instanceOf ClassLike) {
                return new PhpClassClass($this->filename, $element);
            } else if ($element instanceOf Namespace_) {
                return new PhpClassNamespace($this->filename, $element);
            }
        }
        // クラスが含まれていないファイル
        throw new \Exception('not found class.' . $this->filename);
    }

    public function getInfo(): \stdClass {
        $data = (object)[
            'type' => $this->class->getClassType(),
            'properties' => $this->class->getProperties(),
        ];
        return $data;
    }
}
