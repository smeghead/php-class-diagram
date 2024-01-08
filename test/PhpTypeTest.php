<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Smeghead\PhpClassDiagram\Php\PhpType;

final class PhpTypeTest extends TestCase
{
    public function testEquals_different_name(): void
    {
        $sut = new PhpType(['hoge'], '', 'Product');
        $other = new PhpType(['hoge'], '', 'ProductXXX');

        $this->assertFalse($sut->equals($other));
    }
    public function testEquals_different_namespace(): void
    {
        $sut = new PhpType(['hoge'], '', 'Product');
        $other = new PhpType(['hoge', 'fuga'], '', 'Product');

        $this->assertFalse($sut->equals($other));
    }
    public function testEquals_same_name(): void
    {
        $sut = new PhpType(['hoge'], '', 'Product');
        $other = new PhpType(['hoge'], '', 'Product');

        $this->assertTrue($sut->equals($other));
    }
    public function testEquals_braces(): void
    {
        $sut = new PhpType(['hoge'], '', 'Product');
        $other = new PhpType(['hoge'], '', 'Product[]');

        $this->assertTrue($sut->equals($other));
    }
    public function testEquals_array_expression(): void
    {
        $sut = new PhpType(['hoge'], '', 'Product');
        $other = new PhpType(['hoge'], '', 'array<Product>');

        $this->assertTrue($sut->equals($other));
    }
    public function testEquals_array_expression_int_and_product(): void
    {
        $sut = new PhpType(['hoge'], '', 'Product');
        $other = new PhpType(['hoge'], '', 'array<int, Product>');

        $this->assertTrue($sut->equals($other));
    }
    public function testEquals_array_expression_string_and_product(): void
    {
        $sut = new PhpType(['hoge'], '', 'Product');
        $other = new PhpType(['hoge'], '', 'array<string, Product>');

        $this->assertTrue($sut->equals($other));
    }
}
