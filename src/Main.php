<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use Symfony\Component\Finder\Finder;
use Smeghead\PhpClassDiagram\ {
    Relation,
    PhpReflection,
};
use Smeghead\PhpClassDiagram\DiagramElement\Entry;

class Main {
    public function __construct(string $directory) {
        $finder = new Finder();
        $finder->files()->in($directory);
        $finder->files()->name('*.php');
        $entries = [];
        foreach ($finder as $file) {
            try {
                $reflection = new PhpReflection($file->getPathname());
                $entries[] = new Entry($file->getRelativePath(), $reflection->getInfo());
            } catch (Exception $e) {
                fputs(STDERR, $e->getMessage());
            }
        }
        $relation = new Relation($entries);
        echo implode("\r\n", $relation->dump()) . "\r\n";
    }
}
