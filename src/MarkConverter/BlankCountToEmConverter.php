<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class BlankCountToEmConverter implements IBlankCountConverter
{
    public function convert(int $blankcount): string
    {
        if ($blankcount <= 1) {
            return '';
        }

        return ' style="margin-top:'.$blankcount.'em"';
    }
}
