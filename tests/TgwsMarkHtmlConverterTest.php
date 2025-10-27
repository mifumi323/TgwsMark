<?php

namespace Mifumi323\TgwsMark;

use Mifumi323\TgwsMark\MarkConverter\BlankCountToEmConverter;
use Mifumi323\TgwsMark\MarkConverter\ContentHtmlConverterPreserveText;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;

require_once __DIR__.'/TgwsMarkTest.php';

class TgwsMarkHtmlConverterTest extends \PHPUnit\Framework\TestCase
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
        $converter = new TgwsMarkHtmlConverter();
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
        $converter = new TgwsMarkHtmlConverter(headingConverter: $headingToFixedTagHtmlConverter);
        $input = "*Heading 1\n\n**Heading 2#custom-hash\n\n\n***Heading 3\nNormal paragraph.";
        $expected = '<div class="fixed-heading">Heading 1</div>'.
            '<div class="fixed-heading" id="custom-hash">Heading 2<a href="#custom-hash" class="hashlink" title="「Heading 2」の位置へのリンク">#</a></div>'.
            '<div class="fixed-heading" style="margin-top:2em">Heading 3</div>'.
            '<p>Normal paragraph.</p>';
        $actual = $converter->convert($input);
        Assert::assertSame($expected, $actual);
    }
}
