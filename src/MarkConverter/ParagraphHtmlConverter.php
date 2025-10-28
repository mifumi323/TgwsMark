<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class ParagraphHtmlConverter implements IParagraphConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter)
    {
    }

    public function open(int $blankcount): string
    {
        return '<p'.$this->blankCountConverter->convert($blankcount).'>';
    }

    public function close(): string
    {
        return '</p>';
    }

    public function break(): string
    {
        return '<br>';
    }
}
