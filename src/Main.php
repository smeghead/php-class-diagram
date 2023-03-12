<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram;

use Symfony\Component\Finder\Finder;
use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\{
    Entry,
    Relation,
};
use Smeghead\PhpClassDiagram\Php\PhpReader;

class Main
{
    const VERSION = 'v0.5.0';

    public function __construct(string $directory, Options $options)
    {
        $finder = new Finder();
        $finder->files()->in($directory);
        $finder->files()->name($options->includes());
        $excludes = $options->excludes();
        if (count($excludes) > 0) {
            $finder->files()->notName($excludes)->notPath($excludes);
        }
        $entries = [];
        foreach ($finder as $file) {
            try {
                $reflections = PhpReader::parseFile(realpath($directory), $file->getRealPath(), $options);
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
            case OPTIONS::DIAGRAM_JIG:
                echo implode("\r\n", $relation->dump()) . "\r\n";
                echo implode("\r\n", $relation->dumpPackages()) . "\r\n";
                break;
            case OPTIONS::DIAGRAM_DIVSION:
                echo implode("\r\n", $relation->dumpDivisions()) . "\r\n";
                break;
            default:
                throw new \Exception('invalid diagram.');
        }
    }
}
