<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class TableHtmlConverter implements ITableConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter)
    {
    }

    public function open(int $blankcount): string
    {
        return '<table'.$this->blankCountConverter->convert($blankcount).'>';
    }

    public function close(): string
    {
        return '</table>';
    }

    public function rowOpen(): string
    {
        return '<tr>';
    }

    public function rowClose(): string
    {
        return '</tr>';
    }

    public function cellOpen(bool $isHeader, string $attributes): string
    {
        $tag = $isHeader ? 'th' : 'td';

        return '<'.$tag.$attributes.'>';
    }

    public function cellClose(bool $isHeader): string
    {
        $tag = $isHeader ? 'th' : 'td';

        return '</'.$tag.'>';
    }

    public function headerRowOpen(): string
    {
        return '<thead>';
    }

    public function headerRowClose(): string
    {
        return '</thead>';
    }
}
