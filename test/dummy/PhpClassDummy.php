<?php declare(strict_types=1);

use PhpParser\Node\ {
    Stmt,
};
use Smeghead\PhpClassDiagram\Php\ {
    PhpClass,
    PhpType,
    PhpProperty,
};
require_once(__DIR__ . '/PhpMethodDummy.php');
require_once(__DIR__ . '/PhpPropertyDummy.php');

/**
 * Dummy Class for tests.
 */
class PhpClassDummy extends PhpClass {
    public function __construct(string $filename, Stmt $syntax = null) {
        $d = json_decode($filename);
        if ($d === null) {
            throw new \Exception('failed to decode josn.' . $this->getJsonError());
        }
        $this->data = $d;
    }

    public function getClassType(): PhpType {
        return new PhpType($this->data->type->namespace, $this->data->type->meta, $this->data->type->name);
    }

    /**
     * @return PhpType[] use一覧
     */
    public function getUses(): array {
        $uses = [];
        return $uses;
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    public function getProperties(): array {
        $props = [];
        foreach ($this->data->properties as $p) {
            $props[] = new PhpPropertyDummy($p->name, new PhpType($p->type->namespace, '', $p->type->name), $p->modifier);
        }
        return $props;
    }

    /**
     * @return PhpProperty[] プロパティ一覧
     */
    protected function getPropertiesFromSyntax(): array {
        throw new Exception('not implement.');
    }

    public function getMethods(): array {
        $methods = [];
        foreach ($this->data->methods as $m) {
            $methods[] = new PhpMethodDummy($m);
        }
        return $methods;
    }

    public function getExtends(): array {
        $namespace = [];
        $extends = [];
        if ( ! empty($this->data->extends)) {
            foreach ($this->data->extends as $extend) {
                $extends[] = new PhpType($extend->namespace, $extend->meta, $extend->name);
            }
        }
        return $extends;
    }

    private function getJsonError() {
        switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return ' - No errors';
            break;
        case JSON_ERROR_DEPTH:
            return ' - Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            return ' - Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            return ' - Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            return ' - Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            return ' - Unknown error';
            break;
        }
    }
}
