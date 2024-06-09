<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram;

use RuntimeException;
use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\{Entry, Relation,};
use Smeghead\PhpClassDiagram\Php\PhpReader;
use Symfony\Component\Finder\Finder;

final class Main
{
    public const VERSION = 'v1.3.0';

    public function __construct(
        private string $directory,
        private Options $options,
    ) {
    }

    public function run(): void
    {
        $finder = $this->createFinder();
        $entries = $this->findEntries($finder);

        $relation = new Relation($entries, $this->options);
        $this->renderRelations($relation);
    }

    private function createFinder(): Finder
    {
        $finder = new Finder();
        $finder->files()->in($this->directory);
        $finder->files()->name($this->options->includes());
        $excludes = $this->options->excludes();

        if (count($excludes) > 0) {
            $finder->files()->notName($excludes)->notPath($excludes);
        }

        return $finder;
    }

    /**
     * @return list<Entry>
     */
    private function findEntries(Finder $finder): array
    {
        $entries = [];
        foreach ($finder as $file) {
            try {
                $reflections = PhpReader::parseFile(
                    realpath($this->directory),
                    $file->getRealPath(),
                    $this->options
                );
                foreach ($reflections as $reflection) {
                    $entries[] = new Entry($file->getRelativePath(), $reflection->getInfo(), $this->options);
                }
            } catch (\Exception $e) {
                fwrite(STDERR, $e->getMessage() . "\r\n");
            }
        }
        return $entries;
    }

    private function renderRelations(Relation $relation): void
    {
        switch ($this->options->diagram()) {
            case Options::DIAGRAM_CLASS:
                echo implode("\r\n", $relation->dump()) . "\r\n";
                break;
            case OPTIONS::DIAGRAM_PACKAGE:
                echo implode("\r\n", $relation->dumpPackages()) . "\r\n";
                break;
            case OPTIONS::DIAGRAM_JIG:
                echo implode("\r\n", $relation->dump()) . "\r\n";
                echo implode("\r\n", $relation->dumpPackages()) . "\r\n";
                echo implode("\r\n", $relation->dumpDivisions()) . "\r\n";
                break;
            case OPTIONS::DIAGRAM_DIVSION:
                echo implode("\r\n", $relation->dumpDivisions()) . "\r\n";
                break;
            default:
                throw new RuntimeException('invalid diagram.');
        }
    }
}
