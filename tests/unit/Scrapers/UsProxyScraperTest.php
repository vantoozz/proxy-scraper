<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\HttpClient\PredefinedDummyHttpClient;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\UsProxyScraper;

final class UsProxyScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new UsProxyScraper(new FailingDummyHttpClient('error message'));
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new UsProxyScraper(
            new PredefinedDummyHttpClient(
                '<table id="proxylisttable"><tbody><tr><td>46.101.55.200</td><td>8118</td></tr></table>'
            )
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(UsProxyScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new UsProxyScraper(
            new PredefinedDummyHttpClient(
                '<table id="proxylisttable"><tbody><tr><td>46.101.55.200</td><td>8118</td></tr></table>'
            )
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('46.101.55.200:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new UsProxyScraper(
            new PredefinedDummyHttpClient(
                '<table id="proxylisttable"><tbody><tr><td>111</td><td>111</td></tr></table>'
            )
        );

        static::assertNull($scraper->get()->current());
    }
}
