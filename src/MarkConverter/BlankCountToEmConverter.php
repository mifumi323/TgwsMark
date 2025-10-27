<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class BlankCountToEmConverter implements IBlankCountConverter
{
    /**
     * 空行数に応じた文字列を返します。
     *
     * @param int $blankcount 空行数
     */
    public function convert(int $blankcount): string
    {
        if ($blankcount <= 1) {
            return '';
        }

        return ' style="margin-top:'.$blankcount.'em"';
    }
}
