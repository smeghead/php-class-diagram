<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Config;

class Arguments
{
    private string $directory;

    /**
     * @param string[] $arguments arguments string array
     */
    public function __construct(array $arguments)
    {
        if (count($arguments) === 0) {
            throw new ConfigException('not specified php source file.');
        }
        $directory = array_shift($arguments);
        if ( ! is_dir($directory)) {
            throw new ConfigException(sprintf('specified directory dose not exists. directory: %s', $directory));
        }
        $this->directory = $directory;
    }

    public function getDirectory(): string {
        return $this->directory;
    }
}
