<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface IDetailsConverter
{
    /**
     * 詳細折り畳み要素が開始するときに出力される文字列を返します。
     *
     * @param int    $blankcount 要素前の空行数
     * @param string $summary    折り畳み見出しの内容
     * @param string $hash       折り畳み要素のハッシュ値
     */
    public function open(int $blankcount, string $summary, string $hash): string;

    /** 詳細折り畳み要素が終了するときに出力される文字列を返します。 */
    public function close(): string;
}
