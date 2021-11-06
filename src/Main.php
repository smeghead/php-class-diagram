<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use Symfony\Component\Finder\Finder;
use Smeghead\PhpClassDiagram\ {
    Options,
    Relation,
    PhpReflection,
};
use Smeghead\PhpClassDiagram\DiagramElement\Entry;

class Main {
    public function __construct(string $directory, Options $options) {
        $finder = new Finder();
        $finder->files()->in($directory);
        $finder->files()->name('*.php');
        $entries = [];
        foreach ($finder as $file) {
            try {
                $reflection = new PhpReflection($file->getPathname(), $options);
                $entries[] = new Entry($file->getRelativePath(), $reflection->getInfo(), $options);
            } catch (\Exception $e) {
                fputs(STDERR, $e->getMessage() . "\r\n");
            }
        }
        $relation = new Relation($entries, $options);
        echo implode("\r\n", $relation->dump()) . "\r\n";
    }
}
