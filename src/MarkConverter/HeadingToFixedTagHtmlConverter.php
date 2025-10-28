<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class HeadingToFixedTagHtmlConverter implements IHeadingConverter
{
    public function __construct(public string $tag, public IBlankCountConverter $blankCountConverter, public IContentConverter $contentConverter, public string $headattr)
    {
        if (strlen($this->headattr) > 0) {
            $this->headattr = ' '.trim($this->headattr);
        }
    }

    public function convert(int $level, int $blankcount, string $content, string $hash): string
    {
        if (strlen($hash) > 0) {
            $hash_link = '<a href="#'.$this->contentConverter->convertAttributeValueContent($hash).'" class="hashlink" title="「'.$this->contentConverter->convertAttributeValueContent(strip_tags($content)).'」の位置へのリンク">#</a>';
            $hash_attr = ' id="'.$this->contentConverter->convertAttributeValueContent($hash).'"';
        } else {
            $hash_link = '';
            $hash_attr = '';
        }

        return '<'.$this->tag.$this->contentConverter->convertAttributesInTagContent($this->headattr).$hash_attr.$this->blankCountConverter->convert($blankcount).'>'.
            $this->contentConverter->convertTextContent($content).
            $hash_link.'</'.$this->tag.'>';
    }
}
