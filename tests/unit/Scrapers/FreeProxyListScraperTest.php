<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;
use Vantoozz\ProxyScrapper\HttpClient;
use Vantoozz\ProxyScrapper\Proxy;
use Vantoozz\ProxyScrapper\Scrapers\FreeProxyListScraper;

final class FreeProxyListScraperTest extends TestCase
{
    /**
     * @test
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     * @expectedExceptionMessage error message
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        /** @var HttpClient|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willThrowException(new HttpClientException('error message'));

        $scraper = new FreeProxyListScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        /** @var HttpClient|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('<table id="proxylisttable"><tbody><tr><td>46.101.55.200</td><td>8118</td></tr></table>');

        $scraper = new FreeProxyListScraper($httpClient);
        $proxy = $scraper->get()->current();

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertSame('46.101.55.200:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        /** @var HttpClient|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('<table id="proxylisttable"><tbody><tr><td>111</td><td>111</td></tr></table>');

        $scraper = new FreeProxyListScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }
}
