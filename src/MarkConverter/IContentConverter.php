<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface IContentConverter
{
    /**
     * テキストコンテンツを変換して返します。
     *
     * @param string $string 変換前のテキストコンテンツ
     */
    public function convertTextContent(string $string): string;

    /**
     * コードブロック内のテキストコンテンツを変換して返します。
     *
     * @param string $string 変換前のコードブロック内テキストコンテンツ
     */
    public function convertCodeBlockContent(string $string): string;
    
    /**
     * 属性値コンテンツを変換して返します。
     *
     * @param string $string 変換前の属性値コンテンツ
     */
    public function convertAttributeValueContent(string $string): string;

    /**
     * タグ内コンテンツを変換して返します。
     *
     * @param string $string 変換前のタグ内コンテンツ
     */
    public function convertAttributesInTagContent(string $string): string;
}
