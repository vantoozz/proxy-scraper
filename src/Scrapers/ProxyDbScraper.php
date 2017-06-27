<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

use Symfony\Component\DomCrawler\Crawler as Dom;
use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;
use Vantoozz\ProxyScrapper\Exceptions\ScraperException;
use Vantoozz\ProxyScrapper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScrapper\Proxy;
use Vantoozz\ProxyScrapper\ProxyString;

/**
 * Class ProxyDbScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class ProxyDbScraper implements ScraperInterface
{
    private const PAGE_SIZE = 50;
    private const MAX_OFFSET = 1000;
    private const PAGE_URL = 'http://proxydb.net/?limit=%d&offset=%d';

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
     * @throws \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        $offset = 0;
        $pageSize = self::PAGE_SIZE;
        do {
            yield from $this->getPage($offset, $pageSize);
            $offset += $pageSize;
        } while ($offset <= self::MAX_OFFSET);
    }

    /**
     * @param int $offset
     * @param int $pageSize
     * @return \Generator
     * @throws \RuntimeException if the CssSelector Component is not available
     * @throws \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     */
    private function getPage(int $offset, int $pageSize): \Generator
    {
        try {
            $html = $this->httpClient->get(sprintf(static::PAGE_URL, $pageSize, $offset), []);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $rows = (new Dom($html))->filter('table tbody tr');

        foreach ($rows as $row) {
            try {
                yield $this->makeProxy(new Dom($row));
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * @param Dom $tr
     * @return Proxy
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    private function makeProxy(Dom $tr): Proxy
    {
        return (new ProxyString(trim($tr->filter('td')->eq(0)->text())))->asProxy();
    }
}
