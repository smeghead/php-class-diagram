<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ {
    Namespace_,
    ClassLike,
};
use Smeghead\PhpClassDiagram\Config\Options;

class PhpReader {
    private string $directory;
    private string $filename;
    private PhpClass $class;

    private function __construct(string $directory, string $filename, PhpClass $class) {
        $this->directory = $directory;
        $this->filename = $filename;
        $this->class = $class;
    }

    /**
     * @return PhpReader[]
     */
    public static function parseFile(string $directory, string $filename, Options $options): array {
        $code = file_get_contents($filename);

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
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        $relativePath = mb_substr($filename, mb_strlen($directory) + 1);
        $classes = [];
        foreach (self::getClasses($relativePath, $ast) as $class) {
            $classes[] = new self($directory, $filename, $class);
        }
        return $classes;
    }

    /**
     * @return PhpClass[]
     */
    private static function getClasses(string $relativePath, array $ast): array {
        if (count($ast) === 0) {
            return null;
        }
        $classes = [];
        foreach ($ast as $element) {
            if ($element instanceOf ClassLike) {
                $classes[] = new PhpClass($relativePath, $element, $ast);
            } else if ($element instanceOf Namespace_) {
                foreach ($element->stmts as $e) {
                    if ($e instanceOf ClassLike) {
                        $classes[] = new PhpClass($relativePath, $e, $ast);
                    }
                }
            }
        }
        return $classes;
    }

    public function getInfo(): PhpClass {
        return $this->class;
    }
}
