<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface ICodeBlockConverter
{
    /**
     * コードブロックが開始するときに出力される文字列を返します。
     *
     * @param string $language   コードブロックの言語
     * @param string $title      コードブロックのタイトル
     * @param int    $blankcount コードブロック前の空行数
     */
    public function open(string $language, string $title, int $blankcount): string;

    /**
     * コードブロックが終了するときに出力される文字列を返します。
     */
    public function close(): string;
}
