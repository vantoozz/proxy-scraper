<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScrapper\Proxy;
use Vantoozz\ProxyScrapper\Scrapers\TextScraper;

/**
 * Class TextScraperTest
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class TextScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new TextScraper("222.111.222.111:8118\n111.222.111.222:8118");
        $proxy = $scraper->get()->current();

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new TextScraper('2312318');

        $this->assertNull($scraper->get()->current());
    }
}
