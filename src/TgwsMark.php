<?php

namespace Mifumi323\TgwsMark;

use Mifumi323\TgwsMark\MarkConverter\BlankCountToEmConverter;
use Mifumi323\TgwsMark\MarkConverter\ContentHtmlConverterPreserveText;
use Mifumi323\TgwsMark\MarkConverter\ContentHtmlConverterSpecifyFunction;
use Mifumi323\TgwsMark\MarkConverter\HeadingToFixedTagHtmlConverter;
use Mifumi323\TgwsMark\MarkConverter\HeadingToHnHtmlConverter;

class TgwsMark
{
    /**
     * TgwsMark→HTML変換を行います。
     *
     * @param  string        $string          変換するTgwsMarkの文字列
     * @param  string        $head            見出しのタグ名。デフォルトは 'h2'。
     *                                        数字部分があると、見出しレベルに応じてh3, h4, ... と変化します。
     * @param  string        $headattr        見出しのタグに追加する属性。デフォルトは空文字列。
     *                                        例: 'class="my-class"'
     *                                        属性値はエスケープされません。
     * @param  callable|null $escape_function HTMLエスケープを行う関数。
     *                                        nullだとエスケープしません。
     *                                        例: htmlspecialchars(...)
     * @return string        変換後のHTML文字列
     */
    public static function toHtml(string $string, string $head = 'h2', string $headattr = '', ?callable $escape_function = null): string
    {
        $contentConverter = isset($escape_function) ?
            new ContentHtmlConverterSpecifyFunction($escape_function) :
            new ContentHtmlConverterPreserveText();
        if (strlen($head) === 2 && ($head[0] === 'h' || $head[0] === 'H') && is_numeric($head[1])) {
            $headingConverter = new HeadingToHnHtmlConverter($head[0], (int) $head[1], new BlankCountToEmConverter(), $contentConverter, $headattr);
        } else {
            $headingConverter = new HeadingToFixedTagHtmlConverter($head, new BlankCountToEmConverter(), $contentConverter, $headattr);
        }
        $converter = new Converter($contentConverter, $headingConverter);

        return $converter->convert($string);
    }

    /** @return string[] */
    public static function splitLine(string $line): array
    {
        $head_split = explode('<<', $line, 2);
        if (count($head_split) > 1) {
            $head = $head_split[0];
            $head_removed = $head_split[1];
        } else {
            $head = '';
            $head_removed = $line;
        }
        $tail_split = explode('>>', $head_removed, 2);
        if (count($tail_split) > 1) {
            $tail = $tail_split[1];
            $content = $tail_split[0];
        } else {
            $tail = '';
            $content = $head_removed;
        }
        $command = substr($content, 0, 1);
        $command_removed = substr($content, 1);

        return [
            $content,
            $command,
            $command_removed,
            $head,
            $tail,
        ];
    }

    /**
     * 改行で分割します。
     *
     * @return string[]
     */
    public static function splitByNewLine(string $value, bool $noempty = true): array
    {
        return preg_split('/\r\n|\r|\n/', $value, -1, $noempty ? PREG_SPLIT_NO_EMPTY : 0);
    }
}
