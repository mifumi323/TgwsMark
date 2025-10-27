<?php

namespace Mifumi323\TgwsMark;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;

class TgwsMarkTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array[]
     */
    public static function toHtmlDataProvider(): array
    {
        return [
            // 基本的なパターン
            ['', ''],
            ['test', '<p>test</p>'],
            ["p1\n\np2", '<p>p1</p><p>p2</p>'],
            ["p1\n\n\np2", '<p>p1</p><p style="margin-top:2em">p2</p>'],
            ["p1\n", '<p>p1</p>'],
            ["\np1", '<p>p1</p>'],
            ["<details><summary><<*netabare>></summary>\nnaiyou>></details>", '<details><summary><h2>netabare</h2></summary><p>naiyou</p></details>'], // パース外パターン
            ["l1\nh<<l2>>t\nl3", '<p>l1<br>hl2<br>tl3</p>'], // パース外パターン
            ["l1\n\n`<details><summary>netabare</summary>\nnaiyou1\nnaiyou2\n`</details>\n\nl2", '<p>l1</p><details><summary>netabare</summary><p>naiyou1<br>naiyou2</p></details><p>l2</p>'], // パース外パターン

            // 組み合わせ網羅
            ["line1\nline2", '<p>line1<br>line2</p>'],
            ["line1\n*heading2", '<p>line1</p><h2>heading2</h2>'],
            ["line1\n-ul2", '<p>line1</p><ul><li>ul2</li></ul>'],
            ["line1\n+ol2", '<p>line1</p><ol><li>ol2</li></ol>'],
            ["line1\n|th2|h", '<p>line1</p><table><thead><tr><th>th2</th></tr></thead></table>'],
            ["line1\n|table2|", '<p>line1</p><table><tr><td>table2</td></tr></table>'],
            ["line1\n`skip2", '<p>line1</p>skip2'],
            ["*heading1\nline2", '<h2>heading1</h2><p>line2</p>'],
            ["*heading1\n*heading2", '<h2>heading1</h2><h2>heading2</h2>'],
            ["*heading1\n-ul2", '<h2>heading1</h2><ul><li>ul2</li></ul>'],
            ["*heading1\n+ol2", '<h2>heading1</h2><ol><li>ol2</li></ol>'],
            ["*heading1\n|th2|h", '<h2>heading1</h2><table><thead><tr><th>th2</th></tr></thead></table>'],
            ["*heading1\n|table2|", '<h2>heading1</h2><table><tr><td>table2</td></tr></table>'],
            ["*heading1\n`skip2", '<h2>heading1</h2>skip2'],
            ["-ul1\nline2", '<ul><li>ul1</li></ul><p>line2</p>'],
            ["-ul1\n*heading2", '<ul><li>ul1</li></ul><h2>heading2</h2>'],
            ["-ul1\n-ul2", '<ul><li>ul1</li><li>ul2</li></ul>'],
            ["-ul1\n+ol2", '<ul><li>ul1</li></ul><ol><li>ol2</li></ol>'],
            ["-ul1\n|th2|h", '<ul><li>ul1</li></ul><table><thead><tr><th>th2</th></tr></thead></table>'],
            ["-ul1\n|table2|", '<ul><li>ul1</li></ul><table><tr><td>table2</td></tr></table>'],
            ["-ul1\n`skip2", '<ul><li>ul1</li></ul>skip2'],
            ["+ol1\nline2", '<ol><li>ol1</li></ol><p>line2</p>'],
            ["+ol1\n*heading2", '<ol><li>ol1</li></ol><h2>heading2</h2>'],
            ["+ol1\n-ul2", '<ol><li>ol1</li></ol><ul><li>ul2</li></ul>'],
            ["+ol1\n+ol2", '<ol><li>ol1</li><li>ol2</li></ol>'],
            ["+ol1\n|th2|h", '<ol><li>ol1</li></ol><table><thead><tr><th>th2</th></tr></thead></table>'],
            ["+ol1\n|table2|", '<ol><li>ol1</li></ol><table><tr><td>table2</td></tr></table>'],
            ["+ol1\n`skip2", '<ol><li>ol1</li></ol>skip2'],
            ["|th1|h\nline2", '<table><thead><tr><th>th1</th></tr></thead></table><p>line2</p>'],
            ["|th1|h\n*heading2", '<table><thead><tr><th>th1</th></tr></thead></table><h2>heading2</h2>'],
            ["|th1|h\n-ul2", '<table><thead><tr><th>th1</th></tr></thead></table><ul><li>ul2</li></ul>'],
            ["|th1|h\n+ol2", '<table><thead><tr><th>th1</th></tr></thead></table><ol><li>ol2</li></ol>'],
            ["|th1|h\n|th2|h", '<table><thead><tr><th>th1</th></tr></thead><thead><tr><th>th2</th></tr></thead></table>'], // HTMLとしては不自然
            ["|th1|h\n|table2|", '<table><thead><tr><th>th1</th></tr></thead><tr><td>table2</td></tr></table>'],
            ["|th1|h\n`skip2", '<table><thead><tr><th>th1</th></tr></thead></table>skip2'],
            ["|table1|\nline2", '<table><tr><td>table1</td></tr></table><p>line2</p>'],
            ["|table1|\n*heading2", '<table><tr><td>table1</td></tr></table><h2>heading2</h2>'],
            ["|table1|\n-ul2", '<table><tr><td>table1</td></tr></table><ul><li>ul2</li></ul>'],
            ["|table1|\n+ol2", '<table><tr><td>table1</td></tr></table><ol><li>ol2</li></ol>'],
            ["|table1|\n|th2|h", '<table><tr><td>table1</td></tr><thead><tr><th>th2</th></tr></thead></table>'], // HTMLとしては不自然
            ["|table1|\n|table2|", '<table><tr><td>table1</td></tr><tr><td>table2</td></tr></table>'],
            ["|table1|\n`skip2", '<table><tr><td>table1</td></tr></table>skip2'],
            ["`skip1\nline2", 'skip1<p>line2</p>'], // HTMLとしては不自然
            ["`skip1\n*heading2", 'skip1<h2>heading2</h2>'], // HTMLとしては不自然
            ["`skip1\n-ul2", 'skip1<ul><li>ul2</li></ul>'], // HTMLとしては不自然
            ["`skip1\n+ol2", 'skip1<ol><li>ol2</li></ol>'], // HTMLとしては不自然
            ["`skip1\n|th2|h", 'skip1<table><thead><tr><th>th2</th></tr></thead></table>'], // HTMLとしては不自然
            ["`skip1\n|table2|", 'skip1<table><tr><td>table2</td></tr></table>'], // HTMLとしては不自然
            ["`skip1\n`skip2", 'skip1skip2'], // HTMLとしては不自然

            // 見出しレベル
            ["*heading1\n**heading2", '<h2>heading1</h2><h3>heading2</h3>'],
            ["*heading1\n**heading2\n***heading3", '<h2>heading1</h2><h3>heading2</h3><h4>heading3</h4>'],

            // テーブル
            ['|*th|td|', '<table><tr><th>th</th><td>td</td></tr></table>'],
            ['|style="text-align:center;">td|', '<table><tr><td style="text-align:center;">td</td></tr></table>'],
            ['|*style="text-align:center;">th|', '<table><tr><th style="text-align:center;">th</th></tr></table>'],
            ['|style="text-align:center;">*th|', '<table><tr><td style="text-align:center;">*th</td></tr></table>'], // *の指定は先に置かないと反映されない

            // 折り畳み
            ['*>', '<details></details>'],
            ['*>summary1', '<details><summary>summary1</summary></details>'],
            ["*>summary1\n*heading1", '<details><summary>summary1</summary><h2>heading1</h2></details>'], // 同レベルの見出しは含む
            ["*>summary1\n**heading1", '<details><summary>summary1</summary><h3>heading1</h3></details>'], // 下位レベルの見出しは含む
            ["**>summary1\n*heading1", '<details><summary>summary1</summary></details><h2>heading1</h2>'], // 上位レベルの見出しが出現したら終わる
            ["*>summary1\n*>summary2", '<details><summary>summary1</summary></details><details><summary>summary2</summary></details>'], // 入れ子はない
            ["**>summary1\n*>summary2", '<details><summary>summary1</summary></details><details><summary>summary2</summary></details>'], // 入れ子はない
            ["*>summary1\n**>summary2", '<details><summary>summary1</summary></details><details><summary>summary2</summary></details>'], // 入れ子はない
            ["**>summary1\n*heading1\n*>summary2", '<details><summary>summary1</summary></details><h2>heading1</h2><details><summary>summary2</summary></details>'], // 終わってから再開
            ["a\n**>\nb\n*\nc", '<p>a</p><details><p>b</p></details><p>c</p>'], // 空の見出し

            // URLフラグメント
            ['*見出し#midasi', '<h2 id="midasi">見出し<a href="#midasi" class="hashlink" title="「見出し」の位置へのリンク">#</a></h2>'],
            ['*<b>見出し</b>#midasi', '<h2 id="midasi"><b>見出し</b><a href="#midasi" class="hashlink" title="「見出し」の位置へのリンク">#</a></h2>'],
            ['*#midasi', ''], // 見出しの内容がなくなるので単に段落終了扱いになる
            ['*>折り畳み#tatami', '<details><summary id="tatami">折り畳み<a href="#tatami" class="hashlink" title="「折り畳み」の位置へのリンク">#</a></summary></details>'],
            ['*><b>折り畳み</b>#tatami', '<details><summary id="tatami"><b>折り畳み</b><a href="#tatami" class="hashlink" title="「折り畳み」の位置へのリンク">#</a></summary></details>'],
            ['*>#tatami', '<details></details>'], // リンク先となるsummaryがないためリンクも生成しない

            // コードブロック
            ["```\ncode line1\ncode line2\n```", "<pre><code>\ncode line1\ncode line2\n</code></pre>"],
            ["line1\n```\ncode line2\ncode line3\n```\nline4", "<p>line1</p><pre><code>\ncode line2\ncode line3\n</code></pre><p>line4</p>"],
            ["line1\n```\ncode line2\n\ncode line3\n```\nline4", "<p>line1</p><pre><code>\ncode line2\n\ncode line3\n</code></pre><p>line4</p>"],
            ["```\ncode line1\ncode line2", "<pre><code>\ncode line1\ncode line2\n</code></pre>"], // 閉じられない場合
            ["```markdown\n# Heading\nprint('Hello, World!')\n```", "<pre><code class=\"language-markdown\">\n# Heading\nprint('Hello, World!')\n</code></pre>"], // 言語指定あり
            ["```:file.php\ncode line1\ncode line2\n```", "<pre title=\"file.php\"><code>\ncode line1\ncode line2\n</code></pre>"], // ファイル名指定
            ["```php:file.php\ncode line1\ncode line2\n```", "<pre title=\"file.php\"><code class=\"language-php\">\ncode line1\ncode line2\n</code></pre>"], // 言語＋ファイル名指定
            ["````\ncode line1\n```\ncode block in code block\n```\ncode line2\n````", "<pre><code>\ncode line1\n```\ncode block in code block\n```\ncode line2\n</code></pre>"], // ネスト
            ["*>summary\n```\ncode line1\ncode line2\n```", "<details><summary>summary</summary><pre><code>\ncode line1\ncode line2\n</code></pre></details>"], // 折り畳み内コードブロック
            ["```\ncode line1\n````\ncode block in code block\n````\ncode line2\n```", "<pre><code>\ncode line1\n````\ncode block in code block\n````\ncode line2\n</code></pre>"], // ネスト逆
            ["*>summary\n```\ncode line1\ncode line2", "<details><summary>summary</summary><pre><code>\ncode line1\ncode line2\n</code></pre></details>"], // 折り畳み内コードブロック（閉じられない場合）
            ["|table|\n```\ncode line1\ncode line2\n```", "<table><tr><td>table</td></tr></table><pre><code>\ncode line1\ncode line2\n</code></pre>"], // 表終了後にコードブロック開始
            ["-list item\n```\ncode line1\ncode line2\n```", "<ul><li>list item</li></ul><pre><code>\ncode line1\ncode line2\n</code></pre>"], // リスト終了後にコードブロック開始
            ["+list item\n```\ncode line1\ncode line2\n```", "<ol><li>list item</li></ol><pre><code>\ncode line1\ncode line2\n</code></pre>"], // 番号付きリスト終了後にコードブロック開始

            // 改行コード
            ["line1\r\nline2", '<p>line1<br>line2</p>'],
            ["line1\rline2", '<p>line1<br>line2</p>'],
        ];
    }

    #[DataProvider('toHtmlDataProvider')]
    public function testToHtml($input, $expected)
    {
        $actual = TgwsMark::toHtml($input);
        Assert::assertSame($expected, $actual);
    }

    public function testToHtmlWithNumberedHead()
    {
        $actual = TgwsMark::toHtml("*heading1\n**heading2", 'h3');
        $expected = '<h3>heading1</h3><h4>heading2</h4>';
        Assert::assertSame($expected, $actual);
    }

    public function testToHtmlWithUnnumberedHead()
    {
        // Hn以外は見出しレベルに対応しない
        $actual = TgwsMark::toHtml("*heading1\n**heading2", 'div');
        $expected = '<div>heading1</div><div>*heading2</div>';
        Assert::assertSame($expected, $actual);
    }

    public function testToHtmlWithHeadWithAttributes()
    {
        $actual = TgwsMark::toHtml("*heading1\n**heading2", headattr: 'class="test"');
        $expected = '<h2 class="test">heading1</h2><h3 class="test">heading2</h3>';
        Assert::assertSame($expected, $actual);
    }

    public function testSplitLine()
    {
        $actual = TgwsMark::splitLine('a<<bc>>d');
        $expected = ['bc', 'b', 'c', 'a', 'd'];
        Assert::assertSame($expected, $actual);
    }
}
