<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface IUnorderedListConverter
{
    /**
     * 箇条書きリストが開始するときに出力される文字列を返します。
     *
     * @param int $blankcount 箇条書きリスト前の空行数
     */
    public function open(int $blankcount): string;

    /** 箇条書きリストが終了するときに出力される文字列を返します。 */
    public function close(): string;

    /**
     * 箇条書きリストの項目が開始するときに出力される文字列を返します。
     */
    public function itemOpen(): string;

    /** 箇条書きリストの項目が終了するときに出力される文字列を返します。 */
    public function itemClose(): string;
}
