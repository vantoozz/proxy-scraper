<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\CheckProxyScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class CheckProxyScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class CheckProxyScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new CheckProxyScraper(
            new FailingDummyHttpClient('error message')
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new CheckProxyScraper(
            new PredefinedDummyHttpClient(json_encode([['addr' => '222.111.222.111:8118']]))
        );

        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        self::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        self::assertSame(CheckProxyScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new CheckProxyScraper(
            new PredefinedDummyHttpClient(json_encode([['addr' => '222.111.222.111:8118']]))
        );

        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        self::assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_makes_many_attempts(): void
    {
        $httpClient = new class implements HttpClientInterface {

            private $timesCalled = 0;

            public function get(string $uri): string
            {
                $this->timesCalled++;
                if (2 > $this->timesCalled) {
                    return json_encode([]);
                }
                return json_encode([['addr' => '222.111.222.111:8118']]);
            }
        };

        $scraper = new CheckProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        self::assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_ip_addresses(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient(json_encode([['addr' => 'some strind']])));

        self::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient(json_encode([['one' => 'some strind']])));

        self::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_data(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient(json_encode([123, 234])));

        self::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_json(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient('some string'));

        self::assertNull($scraper->get()->current());
    }
}
