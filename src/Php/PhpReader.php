<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\{
    Namespace_,
    ClassLike,
};
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Smeghead\PhpClassDiagram\Config\Options;

class PhpReader
{
    private PhpClass $class;

    private function __construct(PhpClass $class)
    {
        $this->class = $class;
    }

    /**
     * @return PhpReader[]
     */
    public static function parseFile(string $directory, string $filename, Options $options): array
    {
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
            $nameResolver = new NameResolver();
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor($nameResolver);
            // Resolve names
            $ast = $nodeTraverser->traverse($ast);
        } catch (Error $error) {
            throw new \Exception("Parse error: {$error->getMessage()} file: {$filename}\n");
        }

        $relativePath = mb_substr($filename, mb_strlen($directory) + 1);
        $classes = [];
        foreach (self::getClasses($relativePath, $ast) as $class) {
            $classes[] = new self($class);
        }
        return $classes;
    }

    /**
     * @param \PhpParser\Node[] $ast
     * @return PhpClass[]|null
     */
    private static function getClasses(string $relativePath, array $ast): ?array
    {
        if (count($ast) === 0) {
            return null;
        }
        $classes = [];
        foreach ($ast as $element) {
            if ($element instanceof ClassLike) {
                $classes[] = new PhpClass($relativePath, $element, $ast);
            } else if ($element instanceof Namespace_) {
                foreach ($element->stmts as $e) {
                    if ($e instanceof ClassLike) {
                        $classes[] = new PhpClass($relativePath, $e, $ast);
                    }
                }
            }
        }
        return $classes;
    }

    public function getInfo(): PhpClass
    {
        return $this->class;
    }
}
