<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ {
    Namespace_,
    ClassLike,
};
use Smeghead\PhpClassDiagram\Php\ {
    PhpClass,
};

class PhpReflection {
    private string $directory;
    private string $filename;
    private PhpClass $class;
    public function __construct(string $directory, string $filename, Options $options) {
        $this->directory = $directory;
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
            $relativePath = mb_substr($this->filename, mb_strlen($this->directory) + 1);
            if ($element instanceOf ClassLike) {
                return new PhpClass($relativePath, $element, $ast);
            } else if ($element instanceOf Namespace_) {
                foreach ($element->stmts as $e) {
                    if ($e instanceOf ClassLike) {
                        return new PhpClass($relativePath, $e, $ast);
                    }
                }
            }
        }
        // クラスが含まれていないファイル
        throw new \Exception('not found class.' . $this->filename);
    }

    public function getInfo(): PhpClass {
        return $this->class;
    }
}
