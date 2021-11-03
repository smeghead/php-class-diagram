<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

class PhpReflection {
    private string $filename;
    private \ReflectionClass $class;
    public function __construct(string $filename) {
        $this->filename = $filename;
        // クラス名に使える文字 https://www.php.net/manual/ja/language.oop5.basic.php
        if ( ! preg_match('/[\/\\\\]([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)\.php/i', $this->filename, $matches)) {
            throw new Exception('invalid filename.');
        }
        $className = $matches[1];
        require_once($this->filename);
        $this->class = new \ReflectionClass($className);
    }

    private function getClassname(): string {
        return $this->class->getName();
    }

    private function getProperties(): array {
        $properties = $this->class->getProperties();

        $props = [];
        foreach ($properties as $p) {
            $props[] = (object)[
                'name' => $p->getName(),
                'type' => $p->getType()->getName(),
                'private' => $p->isPrivate(),
            ];
        }
        return $props;
    }

    public function getInfo(): \stdClass {
        $data = (object)[
            'name' => $this->class->getName(),
            'namespace' => $this->class->getNamespaceName(),
            'properties' => $this->getProperties(),
        ];
        return $data;
    }
}
