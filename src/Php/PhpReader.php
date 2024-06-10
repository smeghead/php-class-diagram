<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use RuntimeException;
use Smeghead\PhpClassDiagram\Config\Options;

final class PhpReader
{
    private function __construct(
        private PhpClass $class,
    ) {
    }

    public function getInfo(): PhpClass
    {
        return $this->class;
    }

    /**
     * @return list<PhpReader>
     */
    public static function parseFile(string $directory, string $filename, Options $options): array
    {
        $code = (string)file_get_contents($filename);

        $targetVersion = match ($options->phpVersion()) {
            'php5' => ParserFactory::PREFER_PHP5,
            'php7', 'php8' => ParserFactory::PREFER_PHP7, // php-parser でまだ php8 がサポートされていない。
            default => throw new RuntimeException(sprintf("invalid php version %s\n", ParserFactory::PREFER_PHP7)),
        };

        $parser = (new ParserFactory)->create($targetVersion);
        try {
            $ast = $parser->parse($code);
            $nameResolver = new NameResolver();
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor($nameResolver);
            // Resolve names
            $ast = $nodeTraverser->traverse($ast);
        } catch (Error $error) {
            throw new RuntimeException(sprintf("Parse error: %s file: %s\n", $error->getMessage(), $filename));
        }

        $relativePath = mb_substr($filename, mb_strlen($directory) + 1);
        $classes = [];
        foreach (self::getClasses($relativePath, $ast) as $class) {
            $classes[] = new self($class);
        }

        return $classes;
    }

    /**
     * @param list<Node> $ast
     *
     * @return list<PhpClass>
     */
    private static function getClasses(string $relativePath, array $ast): array
    {
        if (count($ast) === 0) {
            return [];
        }

        $classes = [];
        foreach ($ast as $element) {
            if ($element instanceof ClassLike) {
                $classes[] = new PhpClass($relativePath, $element, $ast);
                continue;
            }

            if ($element instanceof Namespace_) {
                foreach ($element->stmts as $e) {
                    if ($e instanceof ClassLike) {
                        $classes[] = new PhpClass($relativePath, $e, $ast);
                    }
                }
            }
        }

        return $classes;
    }
}
