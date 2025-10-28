<?php

namespace Mifumi323\TgwsMark\MarkConverter;

/**
 * 指定の関数でエスケープして返すコンバータです。
 */
class ContentHtmlConverterSpecifyFunction implements IContentConverter
{
    /** @var callable */
    public mixed $escapeFunction;

    public function __construct(callable $escapeFunction)
    {
        $this->escapeFunction = $escapeFunction;
    }

    public function convertTextContent(string $string): string
    {
        return ($this->escapeFunction)($string);
    }

    public function convertCodeBlockContent(string $string): string
    {
        return ($this->escapeFunction)($string);
    }

    public function convertAttributeValueContent(string $string): string
    {
        return ($this->escapeFunction)($string);
    }

    public function convertAttributesInTagContent(string $string): string
    {
        return ($this->escapeFunction)($string);
    }
}
