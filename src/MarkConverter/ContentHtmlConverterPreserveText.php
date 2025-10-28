<?php

namespace Mifumi323\TgwsMark\MarkConverter;

/**
 * テキストコンテンツをそのまま出力し、コードブロック内のテキストコンテンツをHTMLエスケープして返すコンバータです。
 */
class ContentHtmlConverterPreserveText implements IContentConverter
{
    public function convertTextContent(string $string): string
    {
        return $string;
    }

    public function convertCodeBlockContent(string $string): string
    {
        return htmlspecialchars($string, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function convertAttributeValueContent(string $string): string
    {
        return $string;
    }

    public function convertAttributesInTagContent(string $string): string
    {
        return $string;
    }
}
