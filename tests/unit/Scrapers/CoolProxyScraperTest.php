<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\CoolProxyScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class CoolProxyScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class CoolProxyScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new CoolProxyScraper(
            new FailingDummyHttpClient('error message')
        );

        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new CoolProxyScraper(
            new PredefinedDummyHttpClient('[{"ip":"177.43.57.48","port":2222},{"ip":"206.189.220.8","port":80}]')
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(CoolProxyScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new CoolProxyScraper(
            new PredefinedDummyHttpClient('[{"ip":"177.43.57.48","port":2222},{"ip":"206.189.220.8","port":80}]')
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('177.43.57.48:2222', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_rows_with_no_ip(): void
    {
        $scraper = new CoolProxyScraper(
            new PredefinedDummyHttpClient('[{"port":2222}]')
        );

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_non_array_rows(): void
    {
        $scraper = new CoolProxyScraper(
            new PredefinedDummyHttpClient('[123]')
        );

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_rows_with_no_port(): void
    {
        $scraper = new CoolProxyScraper(
            new PredefinedDummyHttpClient('[{"ip":"177.43.57.48"}]')
        );

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_bad_json_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Cannot parse json: Syntax error');

        $scraper = new CoolProxyScraper(
            new PredefinedDummyHttpClient('var json = dcvsdjh')
        );

        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_no_data_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('No data');

        $scraper = new CoolProxyScraper(
            new PredefinedDummyHttpClient('123')
        );
        $scraper->get()->current();
    }
}
