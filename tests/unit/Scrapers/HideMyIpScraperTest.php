<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\HideMyIpScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class HideMyIpScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class HideMyIpScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new HideMyIpScraper(
            new FailingDummyHttpClient('error message')
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_unknown_markdown_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Unknown markup');

        $scraper = new HideMyIpScraper(new PredefinedDummyHttpClient('bad markup'));
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_bad_json_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Cannot parse json: Syntax error');

        $scraper = new HideMyIpScraper(new PredefinedDummyHttpClient('var json = dcvsdjh'));
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new HideMyIpScraper(
            new PredefinedDummyHttpClient(file_get_contents(__DIR__ . '/../../fixtures/hideMyIp.html'))
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        self::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        self::assertSame(HideMyIpScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new HideMyIpScraper(
            new PredefinedDummyHttpClient(file_get_contents(__DIR__ . '/../../fixtures/hideMyIp.html'))
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        self::assertSame('218.161.1.189:3128', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new HideMyIpScraper(new PredefinedDummyHttpClient('var json = [{"i":"2323","p":"2323"}] '));

        self::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_formatted_json_items(): void
    {
        $scraper = new HideMyIpScraper(new PredefinedDummyHttpClient('var json = [1,2,3] '));

        self::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_not_filled_json_items(): void
    {
        $scraper = new HideMyIpScraper(new PredefinedDummyHttpClient('var json = [{"a":1}] '));

        self::assertNull($scraper->get()->current());
    }
}
