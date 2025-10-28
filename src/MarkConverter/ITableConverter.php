<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface ITableConverter
{
    /**
     * テーブルが開始するときに出力される文字列を返します。
     *
     * @param int $blankcount 箇条書きリスト前の空行数
     */
    public function open(int $blankcount): string;

    /** テーブルが終了するときに出力される文字列を返します。 */
    public function close(): string;

    /** テーブルの行が開始するときに出力される文字列を返します。 */
    public function rowOpen(): string;

    /** テーブルの行が終了するときに出力される文字列を返します。 */
    public function rowClose(): string;

    /**
     * テーブルのセルが開始するときに出力される文字列を返します。
     *
     * @param bool   $isHeader   ヘッダーセルかどうか
     * @param string $attributes セルの属性文字列
     */
    public function cellOpen(bool $isHeader, string $attributes): string;

    /** テーブルのセルが終了するときに出力される文字列を返します。 */
    public function cellClose(bool $isHeader): string;

    /** テーブルのヘッダー行が開始するときに出力される文字列を返します。 */
    public function headerRowOpen(): string;

    /** テーブルのヘッダー行が終了するときに出力される文字列を返します。 */
    public function headerRowClose(): string;
}
