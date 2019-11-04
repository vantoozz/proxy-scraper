<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\TextScraper;

/**
 * Class TextScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class TextScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_no_source_metric(): void
    {
        $scraper = new TextScraper("222.111.222.111:8118\n111.222.111.222:8118");
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertCount(0, $proxy->getMetrics());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new TextScraper("222.111.222.111:8118\n111.222.111.222:8118");
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new TextScraper('2312318');

        static::assertNull($scraper->get()->current());
    }
}
