<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\ProxyDbScraper;

/**
 * Class ProxyDbScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class ProxyDbScraperTest extends TestCase
{
    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\ScraperException
     * @expectedExceptionMessage error message
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willThrowException(new HttpClientException('error message'));

        $scraper = new ProxyDbScraper($httpClient);
        $scraper->get()->current();
    }
    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\ScraperException
     * @expectedExceptionMessage Unexpected markup
     */
    public function it_throws_an_exception_on_non_html_response(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('some text');

        $scraper = new ProxyDbScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $html  = <<<HTML
<table><tbody><tr>
<td>
<script>
    var n = '1.631.312'.split('').reverse().join('');
    var yy = atob('\x4d\x44\x55\x75\x4e\x6a\x49\x3d'.replace(/\\x([0-9A-Fa-f]{2})/g,function(){return String.fromCharCode(parseInt(arguments[1], 16))}));
    var pp = -14920 + 18048;
    document.write('<a href="/' + n + yy + '/' + pp + '#http" title="lsocit-213.136.105.62.aviso.ci">' + n + yy + String.fromCharCode(58) + pp + '</a>');
    proxies.push(n + yy + String.fromCharCode(58) + pp);
</script>
</td>
</tr></table>
HTML;
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn($html);

        $scraper = new ProxyDbScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(ProxyDbScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {

        $html  = <<<HTML
<table><tbody><tr>
<td>
<script>
    var n = '1.631.312'.split('').reverse().join('');
    var yy = atob('\x4d\x44\x55\x75\x4e\x6a\x49\x3d'.replace(/\\x([0-9A-Fa-f]{2})/g,function(){return String.fromCharCode(parseInt(arguments[1], 16))}));
    var pp = -14920 + 18048;
    document.write('<a href="/' + n + yy + '/' + pp + '#http" title="lsocit-213.136.105.62.aviso.ci">' + n + yy + String.fromCharCode(58) + pp + '</a>');
    proxies.push(n + yy + String.fromCharCode(58) + pp);
</script>
</td>
</tr></table>
HTML;


        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn($html);

        $scraper = new ProxyDbScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('213.136.105.62:3128', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::exactly(67))
            ->method('get')
            ->willReturn('<table><tbody><tr><td>bad proxy string</td></tr></table>');

        $scraper = new ProxyDbScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }
}
