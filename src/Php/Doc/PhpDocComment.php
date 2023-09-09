<?php

declare(strict_types=1);

namespace Smeghead\PhpClassDiagram\Php\Doc;

use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

final class PhpDocComment
{
    private string $text = '';

    public function __construct(Node $stmt)
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
        $firstLine = array_shift($lines);
        return preg_replace('/\@\S+.*/', '', $firstLine);
    }

    public function getVarTypeName(): string {
        $phpDocNode = $this->getParseResult();
        $vars = $phpDocNode->getVarTagValues();
        if (count($vars) > 0) {
            if ( ! empty($vars[0]->type)) {
                return $this->convertUnionExpression($vars[0]->type->__toString());
            }
        }
        return '';
    }

    public function getParamTypeName(string $paramName): string {
        $phpDocNode = $this->getParseResult();
        $paramTags = array_filter($phpDocNode->getParamTagValues(), function(ParamTagValueNode $node) use ($paramName) {
            return $node->parameterName === sprintf('$%s', $paramName);
        }); // ParamTagValueNode[]
        if (count($paramTags) > 0) {
            if ( ! empty($paramTags[0]->type)) {
                return $this->convertUnionExpression($paramTags[0]->type->__toString());
            }
        }
        return '';
    }

    public function getReturnTypeName(): string {
        $phpDocNode = $this->getParseResult();
        $returns = $phpDocNode->getReturnTagValues();
        if (count($returns) > 0) {
            if ( ! empty($returns[0]->type)) {
                return $this->convertUnionExpression($returns[0]->type->__toString());
            }
        }
        return '';
    }

    private function getParseResult(): PhpDocNode
    {
        $lexer = new Lexer();
        $constExprParser = new ConstExprParser();
        $typeParser = new TypeParser($constExprParser);
        $phpDocParser = new PhpDocParser($typeParser, $constExprParser);
        $tokens = new TokenIterator($lexer->tokenize('/** ' . $this->text . ' */'));
        return $phpDocParser->parse($tokens); // PhpDocNode
    }

    private function convertUnionExpression(string $difinition): string
    {
        $difinition = preg_replace('/^\((.*)\)$/', '$1', $difinition);
        $typeStrings = array_map(function(string $x){
            return trim($x);
        }, explode('|', $difinition));
        return implode('|', $typeStrings);
    }
}
