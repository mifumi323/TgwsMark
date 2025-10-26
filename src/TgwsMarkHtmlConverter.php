<?php

namespace Mifumi323\TgwsMark;

use Mifumi323\TgwsMark\MarkConverter\ContentHtmlConverterPreserveText;
use Mifumi323\TgwsMark\MarkConverter\IContentConverter;

class TgwsMarkHtmlConverter
{
    public IContentConverter $contentConverter;

    public function __construct(?IContentConverter $contentConverter = null)
    {
        $this->contentConverter = $contentConverter ?? new ContentHtmlConverterPreserveText();
    }

    public function convert(string $string): string
    {
        if (strlen($headattr) > 0) {
            $headattr = ' '.trim($headattr);
        }
        if (strlen($head) === 2 && ($head[0] === 'h' || $head[0] === 'H') && is_numeric($head[1])) {
            $h_tag = $head[0];
            $h_level = (int) $head[1];
        } else {
            $h_tag = $head;
            $h_level = null;
        }
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
                    $ret .= '</code></pre>';
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
                        $ret .= '</p>';
                    } elseif ($prev === LineType::UnorderedList) {
                        $ret .= '</ul>';
                    } elseif ($prev === LineType::OrderedList) {
                        $ret .= '</ol>';
                    } elseif ($prev === LineType::Table) {
                        $ret .= '</table>';
                    }

                    $code_block_mark = $matches[1];
                    $language = $matches[2] ?? '';
                    $title = $matches[4] ?? '';
                    // コードブロック開始
                    $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line);
                    $raw_line = '';
                    $ret .= '<pre';
                    if (strlen($title) > 0) {
                        $ret .= ' title="'.$this->contentConverter->convertAttributeValueContent($title).'"';
                    }
                    $ret .= '><code';
                    if (strlen($language) > 0) {
                        $ret .= ' class="language-'.htmlspecialchars($language).'"';
                    }
                    $ret .= ">\n";
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
            $style = ($blankcount > 1) ? (' style="margin-top:'.$blankcount.'em"') : '';
            if ($first === '*' || strlen($line_content) === 0) {
                // 見出し
                if ($first === '*') {
                    // 見出しレベルを先に計算しておく
                    if (isset($h_level)) {
                        $l = $h_level;
                        while ($second !== '' && $second[0] === '*') {
                            $second = substr($second, 1);
                            $l++;
                        }
                    } else {
                        $l = null;
                    }
                }
                if ($prev === LineType::Paragraph) {
                    $ret .= '</p>';
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= '</ul>';
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= '</ol>';
                } elseif ($prev === LineType::Table) {
                    $ret .= '</table>';
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
                    $h = isset($l) ? $h_tag.$l : $head;
                    if ($second[0] !== '>') {
                        if (strlen($hash) > 0) {
                            $hash_link = '<a href="#'.$hash.'" class="hashlink" title="「'.htmlspecialchars(strip_tags($second)).'」の位置へのリンク">#</a>';
                            $hash_attr = ' id="'.$hash.'"';
                        } else {
                            $hash_link = '';
                            $hash_attr = '';
                        }
                        $ret .= '<'.$h.$headattr.$hash_attr.$style.'>'.$this->contentConverter->convertTextContent($second).$hash_link.'</'.$h.'>';
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
                    $ret .= '</p>';
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= '</ol>';
                } elseif ($prev === LineType::Table) {
                    $ret .= '</table>';
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';

                if ($prev !== LineType::UnorderedList) {
                    $ret .= '<ul'.$style.'>';
                }
                $ret .= '<li>'.$this->contentConverter->convertTextContent($second).'</li>';
                $prev = LineType::UnorderedList;
            } elseif ($first === '+') {
                // リスト
                if ($prev === LineType::Paragraph) {
                    $ret .= '</p>';
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= '</ul>';
                } elseif ($prev === LineType::Table) {
                    $ret .= '</table>';
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';

                if ($prev !== LineType::OrderedList) {
                    $ret .= '<ol'.$style.'>';
                }
                $ret .= '<li>'.$this->contentConverter->convertTextContent($second).'</li>';
                $prev = LineType::OrderedList;
            } elseif ($first === '|' && (str_ends_with($second, '|') || str_ends_with($second, '|h'))) {
                // 表
                if ($prev === LineType::Paragraph) {
                    $ret .= '</p>';
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= '</ul>';
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= '</ol>';
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';

                if ($prev !== LineType::Table) {
                    $ret .= '<table'.$style.'>';
                }
                $cells = explode('|', $second);
                $last = $cells[count($cells) - 1];
                $tablehead = false;
                if ($last === 'h') {
                    $tablehead = true;
                }
                array_pop($cells);

                if ($tablehead) {
                    $ret .= '<thead>';
                }
                $ret .= '<tr>';
                foreach ($cells as $cell) {
                    $td = $tablehead ? 'th' : 'td';
                    if (str_starts_with($cell, '*')) {
                        $td = 'th';
                        $cell = substr($cell, 1);
                    }
                    if (preg_match('/^([^<[]+)>/', $cell, $matches)) {
                        $tdargs = ' '.trim($matches[1]);
                        $cell = substr($cell, strlen($matches[0]));
                    } else {
                        $tdargs = '';
                    }
                    $ret .= '<'.$td.$this->contentConverter->convertAttributesInTagContent($tdargs).'>'.$this->contentConverter->convertTextContent(trim($cell)).'</'.$td.'>';
                }
                $ret .= '</tr>';
                if ($tablehead) {
                    $ret .= '</thead>';
                }

                $prev = LineType::Table;
            } else {
                // 通常の文章
                if ($prev === LineType::Paragraph) {
                    $ret .= '<br>';
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= '</ul>';
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= '</ol>';
                } elseif ($prev === LineType::Table) {
                    $ret .= '</table>';
                } else {
                }
                $ret .= $this->contentConverter->convertTextContent($next_tail.$raw_line.$raw_head);
                $raw_line = '';
                if ($prev === LineType::Paragraph) {
                } elseif ($prev === LineType::UnorderedList) {
                    $ret .= '<p>';
                } elseif ($prev === LineType::OrderedList) {
                    $ret .= '<p>';
                } elseif ($prev === LineType::Table) {
                    $ret .= '<p>';
                } else {
                    $ret .= '<p'.$style.'>';
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
            $ret .= '</p>';
        } elseif ($prev === LineType::UnorderedList) {
            $ret .= '</ul>';
        } elseif ($prev === LineType::OrderedList) {
            $ret .= '</ol>';
        } elseif ($prev === LineType::Table) {
            $ret .= '</table>';
        } elseif ($prev === LineType::CodeBlock) {
            // コードブロックが閉じられずに終わった場合は閉じる
            $ret .= $this->contentConverter->convertCodeBlockContent($next_tail.$raw_line);
            $ret .= '</code></pre>';
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
