<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Symfony\Component\DomCrawler\Crawler as Dom;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Class CoolProxyScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class CoolProxyScraper implements ScraperInterface
{
    private const MAX_PAGES_COUNT = 100;
    private const PAGE_URL = 'https://www.cool-proxy.net/proxies/http_proxy_list/page:%d';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * FreeProxyListScraper constructor.
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return \Generator|Proxy[]
     * @throws \RuntimeException
     * @throws \Vantoozz\ProxyScraper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        $page = 1;
        do {
            try {
                yield from $this->getPage($page);
            } catch (HttpClientException $e) {
                break;
            }
            $page++;
        } while ($page <= static::MAX_PAGES_COUNT);
    }

    /**
     * @param int $page
     * @return \Generator
     * @throws \Vantoozz\ProxyScraper\Exceptions\HttpClientException
     * @throws \RuntimeException if the CssSelector Component is not available
     */
    private function getPage(int $page): \Generator
    {

        $html = $this->httpClient->get(sprintf(static::PAGE_URL, $page));

        $rows = (new Dom($html))->filter('table tr');

        foreach ($rows as $row) {
            try {
                yield $this->makeProxy(new Dom($row));
            } catch (\Throwable $e) {
                continue;
            }
        }
    }

    /**
     * @param Dom $row
     * @return Proxy
     * @throws \Throwable
     */
    private function makeProxy(Dom $row): Proxy
    {
        $ipv4 = base64_decode(str_rot13(explode('"', $row->filter('td')->eq(0)->text())[1]));

        $port = (int)$row->filter('td')->eq(1)->text();

        $proxy = new Proxy(new Ipv4($ipv4), new Port($port));
        $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));

        return $proxy;
    }
}
