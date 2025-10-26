<?php

namespace Mifumi323\TgwsMark\MarkConverter;

/**
 * テキストコンテンツをそのまま出力し、コードブロック内のテキストコンテンツをHTMLエスケープして返すコンバータです。
 */
class ContentHtmlConverterPreserveText implements IContentConverter
{
    /**
     * テキストコンテンツを変換して返します。
     *
     * @param string $string 変換前のテキストコンテンツ
     */
    public function convertTextContent(string $string): string
    {
        return $string;
    }

    /**
     * コードブロック内のテキストコンテンツを変換して返します。
     *
     * @param string $string 変換前のコードブロック内テキストコンテンツ
     */
    public function convertCodeBlockContent(string $string): string
    {
        return htmlspecialchars($string, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * 属性値コンテンツを変換して返します。
     *
     * @param string $string 変換前の属性値コンテンツ
     */
    public function convertAttributeValueContent(string $string): string
    {
        return $string;
    }

    /**
     * タグ内コンテンツを変換して返します。
     *
     * @param string $string 変換前のタグ内コンテンツ
     */
    public function convertAttributesInTagContent(string $string): string
    {
        return $string;
    }
}
