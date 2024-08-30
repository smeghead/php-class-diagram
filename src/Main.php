<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram;

use RuntimeException;
use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\Entry;
use Smeghead\PhpClassDiagram\DiagramElement\Relation;
use Smeghead\PhpClassDiagram\Php\PhpReader;
use Symfony\Component\Finder\Finder;

final class Main
{
    public const VERSION = 'v1.3.1';

    public function __construct(
        private string $directory,
        private Options $options,
    ) {
    }

    public function run(): void
    {
        if (!is_dir($this->directory)) {
            $this->runSingleClass();

            return;
        }

        $this->runDefault();
    }

    private function runDefault(): void
    {
        $finder = $this->createFinder();
        $entries = $this->findEntries($finder);
        $this->renderEntries($entries);
    }

    private function runSingleClass(): void
    {
        $finder = $this->createSingleClassFinder();
        $entries = $this->findEntries($finder);
        $this->renderEntries($entries);
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

    private function createSingleClassFinder(): Finder
    {
        $fileDir = explode('/', $this->directory);
        $fileName = array_pop($fileDir);
        $fileDir = implode('/', $fileDir);

        $finder = new Finder();
        $finder->files()->in($fileDir);
        $finder->files()->name([
            $fileName,
        ]);

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
                    (string)realpath($this->directory),
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

    /**
     * @param list<Entry> $entries
     */
    private function renderEntries(array $entries): void
    {
        $relation = new Relation($entries, $this->options);

        match ($this->options->diagram()) {
            Options::DIAGRAM_CLASS => $this->renderDiagramClass($relation),
            Options::DIAGRAM_PACKAGE => $this->renderDiagramPackage($relation),
            Options::DIAGRAM_JIG => $this->renderDiagramJig($relation),
            Options::DIAGRAM_DIVISION => $this->renderDiagramDivision($relation),
            default => throw new RuntimeException('invalid diagram.')
        };
    }

    private function renderDiagramClass(Relation $relation): void
    {
        echo implode("\r\n", $relation->dump()) . "\r\n";
    }

    private function renderDiagramPackage(Relation $relation): void
    {
        echo implode("\r\n", $relation->dumpPackages()) . "\r\n";
    }

    private function renderDiagramJig(Relation $relation): void
    {
        echo implode("\r\n", $relation->dump()) . "\r\n";
        echo implode("\r\n", $relation->dumpPackages()) . "\r\n";
        echo implode("\r\n", $relation->dumpDivisions()) . "\r\n";
    }

    private function renderDiagramDivision(Relation $relation): void
    {
        echo implode("\r\n", $relation->dumpDivisions()) . "\r\n";
    }
}
