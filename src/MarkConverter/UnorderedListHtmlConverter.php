<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class UnorderedListHtmlConverter implements IUnorderedListConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter)
    {
    }

    public function open(int $blankcount): string
    {
        return '<ul'.$this->blankCountConverter->convert($blankcount).'>';
    }

    public function close(): string
    {
        return '</ul>';
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
