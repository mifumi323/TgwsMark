<?php

namespace Mifumi323\TgwsMark\MarkConverter;

class DetailsHtmlConverter implements IDetailsConverter
{
    public function __construct(public IBlankCountConverter $blankCountConverter, public IContentConverter $contentConverter)
    {
    }

    public function open(int $blankcount, string $summary, string $hash): string
    {
        $ret = '<details'.$this->blankCountConverter->convert($blankcount).'>';
        if (strlen($summary) > 0) {
            if (strlen($hash) > 0) {
                // 上と同じコードだけど $second の中身が違うよ。
                $hash_link = '<a href="#'.$hash.'" class="hashlink" title="「'.htmlspecialchars(strip_tags($summary)).'」の位置へのリンク">#</a>';
                $hash_attr = ' id="'.$hash.'"';
            } else {
                $hash_link = '';
                $hash_attr = '';
            }
            $ret .= '<summary'.$hash_attr.'>'.$this->contentConverter->convertTextContent($summary).$hash_link.'</summary>';
        }

        return $ret;
    }

    public function close(): string
    {
        return '</details>';
    }
}
