<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram\Php;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ {
    ClassMethod,
    GroupUse,
    Use_,
};

class PhpClassClass extends PhpClass {
    private array $full;

    public function __construct(string $filename, Stmt $syntax, array $full) {
        parent::__construct($filename, $syntax);
        $this->full = $full; // useの情報が欲しいので全体の構文木を保持する。
    }

    public function getClassType(): PhpType {
        return new PhpType([], $this->syntax->getType(), $this->syntax->name->name);
    }

    /**
     * @return PhpType[] use一覧
     */
    public function getUses(): array {
        $uses = [];
        foreach ($this->full as $stmt) {
            if ($stmt instanceOf GroupUse) {
                $prefix = $stmt->prefix->parts;
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->parts;
                    $name = array_pop($parts);
                    $uses[] = new PhpType(array_merge($prefix, $parts), '', $name, $u->alias); 
                }
            } else if ($stmt instanceOf Use_) {
                foreach ($stmt->uses as $u) {
                    $parts = $u->name->parts;
                    $name = array_pop($parts);
                    $uses[] = new PhpType($parts, '', $name, $u->alias); 
                }
            }
        }
        return $uses;
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    protected function getPropertiesFromSyntax(): array {
        return $this->syntax->getProperties();
    }

    public function getMethods(): array {
        $methods = [];
        foreach ($this->syntax->stmts as $stmt) {
            if ($stmt instanceOf ClassMethod) {
                $methods[] = $this->getMethodInfo($stmt);
            }
        }
        return $methods;
    }

    /**
     * @return PhpType[] 継承元と実装元型一覧
     */
    public function getExtends(): array {
        $namespace = [];
        $extends = [];
        if ( ! empty($this->syntax->extends)) {
            $Name = $this->syntax->extends;
            if (is_array($this->syntax->extends)) {
                $Name = $this->syntax->extends[0];
            } 
            $parts = $Name->parts;
            $name = array_pop($parts);
            $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Class', $name);
        }
        if ( ! empty($this->syntax->implements)) {
            foreach ($this->syntax->implements as $i) {
                $parts = $i->parts;
                $name = array_pop($parts);
                $extends[] = new PhpType(array_merge($namespace, $parts), 'Stmt_Interface', $name);
            }
        }
        return $extends;
    }
}
