<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php\Doc;

use PhpParser\Node\Stmt;

class PhpDocComment
{
    private string $text = '';
    public function __construct(Stmt $stmt)
    {
        $doc = $stmt->getDocComment();
        if (!empty($doc)) {
            $str = $doc->getText();
            $str = preg_replace('/\/\*\*?\s*(.*)\s*\*\//s', '$1', $str);
            $str = preg_replace('/^\s*\**\s*/m', '', $str);
            $this->text = trim($str);
        }
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getDescription(): string
    {
        $str = $this->text;
        $str = str_replace(["\r\n", "\r", "\n"], "\n", $str);
        $lines = explode("\n", $str);
        return array_shift($lines);
    }

    public function getVarTypeName(): string {
        if (preg_match('/\@var\s+(\S+)\s.*/', $this->text . ' ', $matches)) {
            return $matches[1];
        }
        return '';
    }

    public function getParamTypeName(string $paramName): string {
        if (preg_match(sprintf('/\@param\s+(\S+)\s+\$%s.*/', $paramName), $this->text . ' ', $matches)) {
            return $matches[1];
        }
        return '';
    }

    public function getReturnTypeName(): string {
        if (preg_match('/\@return\s+(\S+)\s+/', $this->text . ' ', $matches)) {
            return $matches[1];
        }
        return '';
    }
}
