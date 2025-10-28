<?php

namespace Mifumi323\TgwsMark;

use Mifumi323\TgwsMark\MarkConverter\BlankCountToEmConverter;
use Mifumi323\TgwsMark\MarkConverter\CodeBlockHtmlConverter;
use Mifumi323\TgwsMark\MarkConverter\ContentHtmlConverterPreserveText;
use Mifumi323\TgwsMark\MarkConverter\HeadingToHnHtmlConverter;
use Mifumi323\TgwsMark\MarkConverter\ICodeBlockConverter;
use Mifumi323\TgwsMark\MarkConverter\IContentConverter;
use Mifumi323\TgwsMark\MarkConverter\IHeadingConverter;
use Mifumi323\TgwsMark\MarkConverter\IOrderedListConverter;
use Mifumi323\TgwsMark\MarkConverter\IParagraphConverter;
use Mifumi323\TgwsMark\MarkConverter\ITableConverter;
use Mifumi323\TgwsMark\MarkConverter\OrderedListHtmlConverter;
use Mifumi323\TgwsMark\MarkConverter\ParagraphHtmlConverter;
use Mifumi323\TgwsMark\MarkConverter\TableHtmlConverter;
use Mifumi323\TgwsMark\MarkConverter\UnorderedListHtmlConverter;

class TgwsMarkHtmlConverter
{
    public IContentConverter $contentConverter;
    public IHeadingConverter $headingConverter;
    public ICodeBlockConverter $codeBlockConverter;
    public IParagraphConverter $paragraphConverter;
    public UnorderedListHtmlConverter $unorderedListConverter;
    public IOrderedListConverter $orderedListConverter;
    public ITableConverter $tableConverter;

    public function __construct(
        ?IContentConverter $contentConverter = null,
        ?IHeadingConverter $headingConverter = null,
        ?ICodeBlockConverter $codeBlockConverter = null,
        ?IParagraphConverter $paragraphConverter = null,
        ?UnorderedListHtmlConverter $unorderedListConverter = null,
        ?IOrderedListConverter $orderedListConverter = null,
        ?ITableConverter $tableConverter = null,
    ) {
        $blankCountToEmConverter = new BlankCountToEmConverter();
        $this->contentConverter = $contentConverter ?? new ContentHtmlConverterPreserveText();
        $this->headingConverter = $headingConverter ?? new HeadingToHnHtmlConverter('h', 2, $blankCountToEmConverter, $this->contentConverter, '');
        $this->codeBlockConverter = $codeBlockConverter ?? new CodeBlockHtmlConverter($blankCountToEmConverter, $this->contentConverter);
        $this->paragraphConverter = $paragraphConverter ?? new ParagraphHtmlConverter($blankCountToEmConverter);
        $this->unorderedListConverter = $unorderedListConverter ?? new UnorderedListHtmlConverter($blankCountToEmConverter);
        $this->orderedListConverter = $orderedListConverter ?? new OrderedListHtmlConverter($blankCountToEmConverter);
        $this->tableConverter = $tableConverter ?? new TableHtmlConverter($blankCountToEmConverter);
    }

    public function convert(string $string): string
    {
        $ret = '';
        $lines = preg_split('/\r\n|\r|\n/', $string);
        $blankcount = 0;
        $prev = LineType::Header;
        $next_tail = '';
        $raw_line = '';
        /** @var int|null $detail_level */
        $detail_level = null;
        /** @var string|null $code_block_mark */
        $code_block_mark = null;
        foreach ($lines as $line) {
            // コードブロック処理(コードブロック中では他の処理を行わないので最初にやる)
            if ($prev === LineType::CodeBlock) {
                if (trim($line) === $code_block_mark) {
                    // コードブロック終了
                    $ret .= $this->contentConverter->convertCodeBlockContent($next_tail.$raw_line);
                    $raw_line = '';
                    $ret .= $this->codeBlockConverter->close();
                    $prev = LineType::Header;
                    $next_tail = '';
                    $code_block_mark = null;
                    continue;
                } else {
                    // コードブロック内の行
                    $raw_line .= $line."\n";
                    continue;
                }
            } else {
                if (preg_match('/^(`{3,})([\w-]+)?(:(.*))?$/u', $line, $matches)) {
                    if ($prev === LineType::Paragraph) {
                        $ret .= $this->paragraphConverter->close();
                    } elseif ($prev === LineType::UnorderedList) {
                        $ret .= $this->unorderedListConverter->close();
                    } elseif ($prev === LineType::OrderedList) {
                        $ret .= $this->orderedListConverter->close();
                    } elseif ($prev === LineType::Table) {
                        $ret .= $this->tableConverter->close();
                    }

                    $code_block_mark = $matches[1];
                    $language = $matches[2] ?? '';
                    $title = $matches[4] ?? '';
                    // コードブロック開始
                    $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line);
                    $raw_line = '';
                    $ret .= $this->codeBlockConverter->open($language, $title, $blankcount);
                    $prev = LineType::CodeBlock;
                    $next_tail = '';
                    continue;
                }
            }

            [$line_content, $first, $second, $raw_head, $raw_tail] = self::splitLine($line);
            if ($first === '`') {
                // その行の構文解析を行わない
                $raw_line .= $second;
                continue;
            }
            $isblank = false;
                        if ($first === '*' || strlen($line_content) === 0) {
                // 見出し
                if ($first === '*') {
                    // 見出しレベルを先に計算しておく
                    $l = 1;
                    while ($second !== '' && $second[0] === '*') {
                        $second = substr($second, 1);
                        $l++;
                    }
                }
                if ($prev === LineType::Paragraph) {
                    $ret .= $this->paragraphConverter->close();
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= $this->unorderedListConverter->close();
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= $this->orderedListConverter->close();
                } elseif ($prev === LineType::Table) {
                    $ret .= $this->tableConverter->close();
                }
                if (isset($detail_level) && ((isset($l) && $l < $detail_level) || (strlen($second) > 0 && $second[0] === '>'))) {
                    // 折り畳み記法(終了)
                    $ret .= '</details>';
                    $detail_level = null;
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';
                if (preg_match('/#([\w-]+)$/', $second, $matches)) {
                    $hash = $matches[1];
                    $second = substr($second, 0, strlen($second) - strlen($matches[0]));
                } else {
                    $hash = '';
                }
                if (strlen($second) > 0) {
                    if ($second[0] !== '>') {
                        $ret .= $this->headingConverter->convert($l, $blankcount, $second, $hash);
                    } else {
                        // 折り畳み記法(開始)
                        $detail_level = $l ?? -1;
                        $second = substr($second, 1);
                        $ret .= '<details>';
                        if (strlen($second) > 0) {
                            if (strlen($hash) > 0) {
                                // 上と同じコードだけど $second の中身が違うよ。
                                $hash_link = '<a href="#'.$hash.'" class="hashlink" title="「'.htmlspecialchars(strip_tags($second)).'」の位置へのリンク">#</a>';
                                $hash_attr = ' id="'.$hash.'"';
                            } else {
                                $hash_link = '';
                                $hash_attr = '';
                            }
                            $ret .= '<summary'.$hash_attr.'>'.$this->contentConverter->convertTextContent($second).$hash_link.'</summary>';
                        }
                    }
                } else {
                    $isblank = true;
                }
                $prev = LineType::Header;
            } elseif ($first === '-') {
                // リスト
                if ($prev === LineType::Paragraph) {
                    $ret .= $this->paragraphConverter->close();
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= $this->orderedListConverter->close();
                } elseif ($prev === LineType::Table) {
                    $ret .= $this->tableConverter->close();
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';

                if ($prev !== LineType::UnorderedList) {
                    $ret .= $this->unorderedListConverter->open($blankcount);
                }
                $ret .= $this->unorderedListConverter->itemOpen().$this->contentConverter->convertTextContent($second).$this->unorderedListConverter->itemClose();
                $prev = LineType::UnorderedList;
            } elseif ($first === '+') {
                // リスト
                if ($prev === LineType::Paragraph) {
                    $ret .= $this->paragraphConverter->close();
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= $this->unorderedListConverter->close();
                } elseif ($prev === LineType::Table) {
                    $ret .= $this->tableConverter->close();
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';

                if ($prev !== LineType::OrderedList) {
                    $ret .= $this->orderedListConverter->open($blankcount);
                }
                $ret .= $this->orderedListConverter->itemOpen().$this->contentConverter->convertTextContent($second).$this->orderedListConverter->itemClose();
                $prev = LineType::OrderedList;
            } elseif ($first === '|' && (str_ends_with($second, '|') || str_ends_with($second, '|h'))) {
                // 表
                if ($prev === LineType::Paragraph) {
                    $ret .= $this->paragraphConverter->close();
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= $this->unorderedListConverter->close();
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= $this->orderedListConverter->close();
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';

                if ($prev !== LineType::Table) {
                    $ret .= $this->tableConverter->open($blankcount);
                }
                $cells = explode('|', $second);
                $last = $cells[count($cells) - 1];
                $tablehead = false;
                if ($last === 'h') {
                    $tablehead = true;
                }
                array_pop($cells);

                if ($tablehead) {
                    $ret .= $this->tableConverter->headerRowOpen();
                }
                $ret .= $this->tableConverter->rowOpen();
                foreach ($cells as $cell) {
                    $isHeaderCell = $tablehead;
                    if (str_starts_with($cell, '*')) {
                        $isHeaderCell = true;
                        $cell = substr($cell, 1);
                    }
                    if (preg_match('/^([^<[]+)>/', $cell, $matches)) {
                        $tdargs = ' '.trim($matches[1]);
                        $cell = substr($cell, strlen($matches[0]));
                    } else {
                        $tdargs = '';
                    }
                    $ret .= $this->tableConverter->cellOpen($isHeaderCell, $tdargs).
                        $this->contentConverter->convertTextContent(trim($cell)).
                        $this->tableConverter->cellClose($isHeaderCell);
                }
                $ret .= $this->tableConverter->rowClose();
                if ($tablehead) {
                    $ret .= $this->tableConverter->headerRowClose();
                }

                $prev = LineType::Table;
            } else {
                // 通常の文章
                if ($prev === LineType::Paragraph) {
                    $ret .= $this->paragraphConverter->break();
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= $this->unorderedListConverter->close();
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= $this->orderedListConverter->close();
                } elseif ($prev === LineType::Table) {
                    $ret .= $this->tableConverter->close();
                } else {
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';
                if ($prev === LineType::Paragraph) {
                } else {
                    $ret .= $this->paragraphConverter->open($blankcount);
                }
                $ret .= $this->contentConverter->convertTextContent($line_content);
                $prev = LineType::Paragraph;
            }
            if ($isblank) {
                $blankcount++;
            } else {
                $blankcount = 0;
            }
            $next_tail = $raw_tail;
        }
        if ($prev === LineType::Paragraph) {
            $ret .= $this->paragraphConverter->close();
        } elseif ($prev === LineType::UnorderedList) {
            $ret .= $this->unorderedListConverter->close();
        } elseif ($prev === LineType::OrderedList) {
            $ret .= $this->orderedListConverter->close();
        } elseif ($prev === LineType::Table) {
            $ret .= $this->tableConverter->close();
        } elseif ($prev === LineType::CodeBlock) {
            // コードブロックが閉じられずに終わった場合は閉じる
            $ret .= $this->contentConverter->convertCodeBlockContent($next_tail.$raw_line);
            $ret .= $this->codeBlockConverter->close();
            $raw_line = '';
        }
        if (isset($detail_level)) {
            // 折り畳み記法(終了)
            $ret .= '</details>';
        }
        $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line);

        return $ret;
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
}
