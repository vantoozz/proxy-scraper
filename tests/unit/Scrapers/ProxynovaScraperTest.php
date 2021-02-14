<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\ProxynovaScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class ProxynovaScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class ProxynovaScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new ProxynovaScraper(
            new FailingDummyHttpClient('error message')
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new ProxynovaScraper(
            new PredefinedDummyHttpClient(
                '<table id="tbl_proxy_list"><tbody><tr><td>\'46.101.55.200\'</td><td>8118</td></tr></table>'
            )
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        self::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        self::assertSame(ProxynovaScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new ProxynovaScraper(
            new PredefinedDummyHttpClient(
                '<table id="tbl_proxy_list"><tbody><tr><td>\'46.101.55.200\'</td><td>8118</td></tr></table>'
            )
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        self::assertSame('46.101.55.200:8118', (string)$proxy);
    }


    /**
     * @test
     */
    public function it_throws_an_exception_if_unknown_markdown_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Unknown markup');

        $scraper = new ProxynovaScraper(
            new PredefinedDummyHttpClient(
                '<table id="tbl_proxy_list"><tbody><tr><td>111</td><td>111</td></tr></table>'
            )
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new ProxynovaScraper(
            new PredefinedDummyHttpClient(
                '<table id="tbl_proxy_list"><tbody><tr><td>111</td></tr></table>'
            )
        );

        self::assertNull($scraper->get()->current());
    }
}
