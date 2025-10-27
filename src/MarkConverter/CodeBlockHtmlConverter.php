<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class CodeBlockHtmlConverter implements ICodeBlockConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter, public IContentConverter $contentConverter)
    {
    }

    /**
     * コードブロックが開始するときに出力される文字列を返します。
     *
     * @param string $language   コードブロックの言語
     * @param string $title      コードブロックのタイトル
     * @param int    $blankcount コードブロック前の空行数
     */
    public function open(string $language, string $title, int $blankcount): string
    {
        $ret = '';
        $ret .= '<pre';
        if (strlen($title) > 0) {
            $ret .= ' title="'.$this->contentConverter->convertAttributeValueContent($title).'"';
        }
        $ret .= $this->blankCountConverter->convert($blankcount);
        $ret .= '><code';
        if (strlen($language) > 0) {
            $ret .= ' class="language-'.htmlspecialchars($language).'"';
        }
        $ret .= ">\n";

        return $ret;
    }

    /**
     * コードブロックが終了するときに出力される文字列を返します。
     */
    public function close(): string
    {
        return '</code></pre>';
    }
}
