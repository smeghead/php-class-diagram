<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Config\Arguments;
use Smeghead\PhpClassDiagram\Config\ConfigException;

final class ArgumentsTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testNoArguments(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('not specified php source file.');
        $arguments = [];

        $sut = new Arguments($arguments);
    }
    public function testOneExistsArgument(): void
    {
        $arguments = [
            'src/Config',
        ];

        $sut = new Arguments($arguments);
        $this->assertSame('src/Config', $sut->getDirectory());
    }
    public function testOneNotExistsArgument(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('specified directory dose not exists. directory: src/Config___');

        $arguments = [
            'src/Config___',
        ];

        $sut = new Arguments($arguments);
    }
}
