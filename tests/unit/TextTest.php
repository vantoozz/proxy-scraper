<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Text;

/**
 * Class TextTest
 * @package Vantoozz\ProxyScraper\UnitTests
 */
final class TextTest extends TestCase
{
    /**
     * @test
     * @dataProvider htmlDataProvider
     * @param string $string
     * @param bool $expected
     */
    public function it_detects_html(string $string, bool $expected): void
    {
        static::assertEquals((new Text($string))->isHtml(), $expected);
    }

    /**
     * @test
     * @dataProvider xmlDataProvider
     * @param string $string
     * @param bool $expected
     */
    public function it_detects_xml(string $string, bool $expected): void
    {
        static::assertEquals((new Text($string))->isXml(), $expected);
    }

    /**
     * @return array
     */
    public function htmlDataProvider(): array
    {
        return [
            ['<p>some text</p>', true],
            ['some text', false],
        ];
    }

    /**
     * @return array
     */
    public function xmlDataProvider(): array
    {
        return [
            ['<?xml version', true],
            ['some text', false],
        ];
    }
}
