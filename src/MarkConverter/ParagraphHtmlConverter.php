<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class ParagraphHtmlConverter implements IParagraphConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter)
    {
    }

    /**
     * パラグラフが開始するときに出力される文字列を返します。
     *
     * @param int $blankcount パラグラフ前の空行数
     */
    public function open(int $blankcount): string
    {
        return '<p'.$this->blankCountConverter->convert($blankcount).'>';
    }

    /** パラグラフが終了するときに出力される文字列を返します。 */
    public function close(): string
    {
        return '</p>';
    }

    /** パラグラフ内で改行が発生したときに出力される文字列を返します。 */
    public function break(): string
    {
        return '<br>';
    }
}
