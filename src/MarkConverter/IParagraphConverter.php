<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface IParagraphConverter
{
    /**
     * パラグラフが開始するときに出力される文字列を返します。
     *
     * @param int $blankcount パラグラフ前の空行数
     */
    public function open(int $blankcount): string;

    /** パラグラフが終了するときに出力される文字列を返します。 */
    public function close(): string;

    /** パラグラフ内で改行が発生したときに出力される文字列を返します。 */
    public function break(): string;
}
