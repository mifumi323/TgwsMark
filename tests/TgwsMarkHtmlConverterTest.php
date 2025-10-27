<?php

namespace Mifumi323\TgwsMark;

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
}
