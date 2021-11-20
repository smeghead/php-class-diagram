<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use Symfony\Component\Finder\Finder;
use Smeghead\PhpClassDiagram\DiagramElement\Entry;

class Main {
    const VERSION = 'v0.0.3';

    public function __construct(string $directory, Options $options) {
        $finder = new Finder();
        $finder->files()->in($directory);
        $finder->files()->name('*.php');
        $entries = [];
        foreach ($finder as $file) {
            try {
                $reflections = PhpReflection::parseFile(realpath($directory), $file->getRealPath(), $options);
                foreach ($reflections as $reflection) {
                  $entries[] = new Entry($file->getRelativePath(), $reflection->getInfo(), $options);
                }
            } catch (\Exception $e) {
                fputs(STDERR, $e->getMessage() . "\r\n");
            }
        }
        $relation = new Relation($entries, $options);
        switch ($options->diagram()) {
        case Options::DIAGRAM_CLASS:
            echo implode("\r\n", $relation->dump()) . "\r\n";
            break;
        case OPTIONS::DIAGRAM_PACKAGE:
            echo implode("\r\n", $relation->dumpPackages()) . "\r\n";
            break;
        default:
            throw new \Exception('invalid diagram.');
        }
    }
}
