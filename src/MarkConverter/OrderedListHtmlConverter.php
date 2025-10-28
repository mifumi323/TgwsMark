<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class OrderedListHtmlConverter implements IOrderedListConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter)
    {
    }

    public function open(int $blankcount): string
    {
        return '<ol'.$this->blankCountConverter->convert($blankcount).'>';
    }

    public function close(): string
    {
        return '</ol>';
    }

    public function itemOpen(): string
    {
        return '<li>';
    }

    public function itemClose(): string
    {
        return '</li>';
    }
}
