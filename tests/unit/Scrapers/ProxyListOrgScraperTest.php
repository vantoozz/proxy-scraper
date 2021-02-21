<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\ProxyListOrgScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class ProxyListOrgScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class ProxyListOrgScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new ProxyListOrgScraper(
            new FailingDummyHttpClient('error message')
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new ProxyListOrgScraper(
            new PredefinedDummyHttpClient(
                '<li><script type="text/javascript">Proxy(\'MTAzLjI1Mi4xMTcuMTAwOjgwODA=\')</script></li>'
            )
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        self::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        self::assertSame(ProxyListOrgScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new ProxyListOrgScraper(
            new PredefinedDummyHttpClient(
                '<li><script type="text/javascript">Proxy(\'MTAzLjI1Mi4xMTcuMTAwOjgwODA=\')</script></li>'
            )
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        self::assertSame('103.252.117.100:8080', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new ProxyListOrgScraper(
            new PredefinedDummyHttpClient(
                '<li><script type="text/javascript">Proxy(\''.base64_encode('103.252.117.100:000').'\')</script></li>'
            )
        );

        self::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_badly_encoded_rows(): void
    {
        $scraper = new ProxyListOrgScraper(
            new PredefinedDummyHttpClient(
                '<li><script type="text/javascript">Proxy(\'&*&%*&%*&%*&\')</script></li>'
            )
        );

        self::assertNull($scraper->get()->current());
    }
}
