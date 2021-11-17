<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

use Smeghead\PhpClassDiagram\Options;
use Smeghead\PhpClassDiagram\DiagramElement\ {
    Namespace_,
};

class Relation {
    private Options $options;
    private Namespace_ $namespace;

    public function __construct(array $entries, Options $options) {
        $this->options = $options;
        Namespace_::init();
        $this->namespace = new Namespace_([], 'ROOT', $options);
        foreach ($entries as $e) {
            $this->namespace->addEntry(preg_split('/[\\\\\/]/', $e->directory), $e);
        }
    }

    public function getNamespace(): Namespace_ {
        return $this->namespace;
    }

    public function dump(): array {
        $lines = ['@startuml'];
        $lines = array_merge($lines, $this->namespace->dump());
        $lines = array_merge($lines, $this->getRelations());
        $lines[] = '@enduml';

        return $lines;
    }

    public function getRelations(): array {
        $entities = $this->namespace->getEntries();
        $relation_expressions = array_map(function($x) use ($entities){
            foreach ($entities as $e) {
                //if ($e->class->getClassType()->name == str_replace('[]', '', $x->to->name)) {
                if ($e->class->getClassType()->equals($x->to)) {
                    return $x->toString($e->class);
                }
            }
            return null;
        }, $this->namespace->getArrows());
        $relation_expressions = array_filter($relation_expressions);
        sort($relation_expressions);
        return $relation_expressions;
    }

    public function dumpPackages(): array {
        $lines = ['@startuml'];
        $lines = array_merge($lines, $this->namespace->dumpPackages());
        $uses = $this->getUses();
        foreach ($uses as $namespace => $us) {
            // パッケージの依存を出力するには、パッケージの形式で出力する必要がある。
            // クラス図では、パッケージを入れ子構造で名前だけ表示したが、
            // パッケージ関連図では、パッケージを入れ子構造でかつ絶対パスの名前で表示したい。
            // クラス図も同じようにした方がいいか？
            //
            // -> クラス図もパッケージ図もパッケージ名は名前でaliasを絶対パスにする。
            // これで良ければ、パッケージ名の重複を気にしなくて済む
            //
            // package DiagramElement as hoge.DiagramElement <<Rectangle>> {
            //     package Php as hoge.DiagramElement.Php <<Rectangle>> {
            //     }
            // }
        }
        $lines[] = '@enduml';

        return $lines;
    }

    public function getUses(): array {
        return $this->namespace->getUses([]);
    }
}
