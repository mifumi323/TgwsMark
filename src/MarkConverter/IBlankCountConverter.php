<?php

namespace Mifumi323\TgwsMark\MarkConverter;

interface IBlankCountConverter
{
    /**
     * 空行数に応じた文字列を返します。
     *
     * @param int $blankcount 空行数
     */
    public function convert(int $blankcount): string;
}
