<?php

namespace Mifumi323\TgwsMark;

class TgwsMark
{
    public static function toHtml(string $string, string $head = 'h2', string $headattr = ''): string
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
        $lines = self::splitByNewLine($string, false);
        $blankcount = 0;
        $prev = '*';
        $next_tail = '';
        $raw_line = '';
        /** @var int|null $detail_level */
        $detail_level = null;
        foreach ($lines as $line) {
            [$line_content, $first, $second, $raw_head, $raw_tail] = self::splitLine($line);
            if ($first === '`') {
                // その行の構文解析を行わない
                $raw_line .= $second;
                continue;
            }
            $isblank = false;
            $style = ($blankcount > 1) ? (' style="margin-top:'.$blankcount.'em"') : '';
            if ($first == '*' || strlen($line_content) == 0) {
                // 見出し
                if ($first == '*') {
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
                if ($prev == 'p') {
                    $ret .= '</p>';
                } elseif ($prev == '-') {
                    $ret .= '</ul>';
                } elseif ($prev == '+') {
                    $ret .= '</ol>';
                } elseif ($prev == '|') {
                    $ret .= '</table>';
                }
                if (isset($detail_level) && ((isset($l) && $l < $detail_level) || (strlen($second) > 0 && $second[0] === '>'))) {
                    // 折り畳み記法(終了)
                    $ret .= '</details>';
                    $detail_level = null;
                }
                $ret .= $next_tail.$raw_line.$raw_head;
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
                        $ret .= '<'.$h.$headattr.$hash_attr.$style.'>'.$second.$hash_link.'</'.$h.'>';
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
                            $ret .= '<summary'.$hash_attr.'>'.$second.$hash_link.'</summary>';
                        }
                    }
                } else {
                    $isblank = true;
                }
                $prev = '*';
            } elseif ($first == '-') {
                // リスト
                if ($prev == 'p') {
                    $ret .= '</p>';
                } elseif ($prev == '+') {
                    $ret .= '</ol>';
                } elseif ($prev == '|') {
                    $ret .= '</table>';
                }
                $ret .= $next_tail.$raw_line.$raw_head;
                $raw_line = '';

                if ($prev != '-') {
                    $ret .= '<ul'.$style.'>';
                }
                $ret .= '<li>'.$second.'</li>';
                $prev = '-';
            } elseif ($first == '+') {
                // リスト
                if ($prev == 'p') {
                    $ret .= '</p>';
                } elseif ($prev == '-') {
                    $ret .= '</ul>';
                } elseif ($prev == '|') {
                    $ret .= '</table>';
                }
                $ret .= $next_tail.$raw_line.$raw_head;
                $raw_line = '';

                if ($prev != '+') {
                    $ret .= '<ol'.$style.'>';
                }
                $ret .= '<li>'.$second.'</li>';
                $prev = '+';
            } elseif ($first == '|' && (str_ends_with($second, '|')||str_ends_with($second, '|h'))) {
                // 表
                if ($prev == 'p') {
                    $ret .= '</p>';
                } elseif ($prev == '-') {
                    $ret .= '</ul>';
                } elseif ($prev == '+') {
                    $ret .= '</ol>';
                }
                $ret .= $next_tail.$raw_line.$raw_head;
                $raw_line = '';

                if ($prev != '|') {
                    $ret .= '<table'.$style.'>';
                }
                $cells = explode('|', $second);
                $last = $cells[count($cells) - 1];
                $tablehead = false;
                if ($last == 'h') {
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
                    $ret .= '<'.$td.$tdargs.'>'.trim($cell).'</'.$td.'>';
                }
                $ret .= '</tr>';
                if ($tablehead) {
                    $ret .= '</thead>';
                }

                $prev = '|';
            } else {
                // 通常の文章
                if ($prev == 'p') {
                    $ret .= '<br>';
                } elseif ($prev == '-') {
                    $ret .= '</ul>';
                } elseif ($prev == '+') {
                    $ret .= '</ol>';
                } elseif ($prev == '|') {
                    $ret .= '</table>';
                } else {
                }
                $ret .= $next_tail.$raw_line.$raw_head;
                $raw_line = '';
                if ($prev == 'p') {
                } elseif ($prev == '-') {
                    $ret .= '<p>';
                } elseif ($prev == '+') {
                    $ret .= '<p>';
                } elseif ($prev == '|') {
                    $ret .= '<p>';
                } else {
                    $ret .= '<p'.$style.'>';
                }
                $ret .= $line_content;
                $prev = 'p';
            }
            if ($isblank) {
                $blankcount++;
            } else {
                $blankcount = 0;
            }
            $next_tail = $raw_tail;
        }
        if ($prev == 'p') {
            $ret .= '</p>';
        } elseif ($prev == '-') {
            $ret .= '</ul>';
        } elseif ($prev == '+') {
            $ret .= '</ol>';
        } elseif ($prev == '|') {
            $ret .= '</table>';
        }
        if (isset($detail_level)) {
            // 折り畳み記法(終了)
            $ret .= '</details>';
        }
        $ret .= $next_tail.$raw_line;

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
