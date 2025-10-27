<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface IHeadingConverter
{
    /**
     * 見出しが開始するときに出力される文字列を返します。
     *
     * @param int    $level      見出しレベル
     * @param int    $blankcount 見出し前の空行数
     * @param string $content    見出しの内容
     * @param string $hash       見出しのハッシュ値
     */
    public function convert(int $level, int $blankcount, string $content, string $hash): string;
}
