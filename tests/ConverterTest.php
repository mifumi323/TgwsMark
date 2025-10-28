<?php

namespace Mifumi323\TgwsMark;

use Mifumi323\TgwsMark\MarkConverter\BlankCountToEmConverter;
use Mifumi323\TgwsMark\MarkConverter\ContentHtmlConverterPreserveText;
use Mifumi323\TgwsMark\MarkConverter\ContentHtmlConverterSpecifyFunction;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;

require_once __DIR__.'/TgwsMarkTest.php';

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array[]
     */
    public static function toHtmlDataProvider(): array
    {
        return TgwsMarkTest::toHtmlDataProvider();
    }

    #[DataProvider('toHtmlDataProvider')]
    public function testConvert($input, $expected)
    {
        $converter = new Converter();
        $actual = $converter->convert($input);
        Assert::assertSame($expected, $actual);
    }

    public function testConvertWithHeadingToFixedTagHtmlConverter()
    {
        $headingToFixedTagHtmlConverter = new MarkConverter\HeadingToFixedTagHtmlConverter(
            'div',
            new BlankCountToEmConverter(),
            new ContentHtmlConverterPreserveText(),
            'class="fixed-heading"',
        );
        $converter = new Converter(headingConverter: $headingToFixedTagHtmlConverter);
        $input = "*Heading 1\n\n**Heading 2#custom-hash\n\n\n***Heading 3\nNormal paragraph.";
        $expected = '<div class="fixed-heading">Heading 1</div>'.
            '<div class="fixed-heading" id="custom-hash">*Heading 2<a href="#custom-hash" class="hashlink" title="「Heading 2」の位置へのリンク">#</a></div>'.
            '<div class="fixed-heading" style="margin-top:2em">**Heading 3</div>'.
            '<p>Normal paragraph.</p>';
        $actual = $converter->convert($input);
        Assert::assertSame($expected, $actual);
    }

    public function testConvertWithContentHtmlConverterSpecifyFunction()
    {
        $contentConverter = new ContentHtmlConverterSpecifyFunction(htmlspecialchars(...));
        $converter = new Converter(contentConverter: $contentConverter);
        $input = "*<h1>TEST</h1>#xxx\n<script>alert('XSS');</script>\n\n```\n<b>code</b>\n```\n| title=\"\">table|";
        $expected = "<h2 id=\"xxx\">&lt;h1&gt;TEST&lt;/h1&gt;<a href=\"#xxx\" class=\"hashlink\" title=\"「TEST」の位置へのリンク\">#</a></h2><p>&lt;script&gt;alert(&#039;XSS&#039;);&lt;/script&gt;</p><pre><code>\n&lt;b&gt;code&lt;/b&gt;\n</code></pre><table><tr><td title=&quot;&quot;>table</td></tr></table>";
        $actual = $converter->convert($input);
        Assert::assertSame($expected, $actual);
    }
}
