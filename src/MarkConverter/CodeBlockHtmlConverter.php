<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class CodeBlockHtmlConverter implements ICodeBlockConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter, public IContentConverter $contentConverter)
    {
    }

    public function open(string $language, string $title, int $blankcount): string
    {
        $ret = '';
        $ret .= '<pre';
        if (strlen($title) > 0) {
            $ret .= ' title="'.$this->contentConverter->convertAttributeValueContent($title).'"';
        }
        $ret .= $this->blankCountConverter->convert($blankcount);
        $ret .= '><code';
        if (strlen($language) > 0) {
            $ret .= ' class="language-'.htmlspecialchars($language).'"';
        }
        $ret .= ">\n";

        return $ret;
    }

    public function close(): string
    {
        return '</code></pre>';
    }
}
