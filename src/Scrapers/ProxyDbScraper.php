<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Symfony\Component\DomCrawler\Crawler as Dom;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;
use Vantoozz\ProxyScraper\Text;

/**
 * Class ProxyDbScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class ProxyDbScraper implements ScraperInterface
{
    private const PAGE_SIZE = 15;
    private const MAX_OFFSET = 1000;
    private const PAGE_URL = 'http://proxydb.net?offset=%d';

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
        $offset = 0;
        $pageSize = self::PAGE_SIZE;
        do {
            yield from $this->getPage($offset);
            $offset += $pageSize;
        } while ($offset <= self::MAX_OFFSET);
    }

    /**
     * @param int $offset
     * @return \Generator
     * @throws \RuntimeException if the CssSelector Component is not available
     * @throws \Vantoozz\ProxyScraper\Exceptions\ScraperException
     */
    private function getPage(int $offset): \Generator
    {
        try {
            $html = $this->httpClient->get(sprintf(static::PAGE_URL, $offset));
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        if (!(new Text($html))->isHtml()) {
            throw new ScraperException('Unexpected markup');
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
     * @param Dom $row
     * @return Proxy
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    private function makeProxy(Dom $row): Proxy
    {
        $encoded = trim($row->filter('td')->eq(0)->text());

        $proxy = (new ProxyString($this->decode($encoded)))->asProxy();
        $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));

        return $proxy;
    }

    /**
     * @param string $encoded
     * @return string
     */
    private function decode(string $encoded): string
    {
        $result = '';

        $matches = [];
        preg_match("/var n = \'(.+)'\.split\(\'\'\)\.reverse\(\)\.join\(\'\'\);/", $encoded, $matches);

        if (array_key_exists(1, $matches)) {
            $result .= strrev($matches[1]);
        }

        preg_match("/var yy = atob\(\'(.+)\'\.replace/", $encoded, $matches);

        if (array_key_exists(1, $matches)) {
            $result .= base64_decode($matches[1]);
        }

        preg_match("/var pp = (.+)\s\+\s(.+);/", $encoded, $matches);

        if (array_key_exists(2, $matches)) {
            $result .= ':' . ((int)$matches[1] + (int)$matches[2]);
        }

        return $result;
    }
}
