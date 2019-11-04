<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Exception;
use Generator;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler as Dom;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;

/**
 * Class AbstractFreeProxyListScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
abstract class AbstractFreeProxyListScraper implements ScraperInterface
{
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
     * @return Generator|Proxy[]
     * @throws RuntimeException if the CssSelector Component is not available
     * @throws ScraperException
     */
    public function get(): Generator
    {
        try {
            $html = $this->httpClient->get($this->baseUrl());
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $rows = (new Dom($html))->filter('#proxylisttable tbody tr');

        foreach ($rows as $row) {
            try {
                yield $this->makeProxy(new Dom($row));
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }
    }

    /**
     * @param Dom $row
     * @return Proxy
     * @throws InvalidArgumentException
     */
    private function makeProxy(Dom $row): Proxy
    {
        try {
            $ipv4 = $row->filter('td')->eq(0)->text();
            $port = (int)$row->filter('td')->eq(1)->text();
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        $proxy = (new ProxyString($ipv4 . ':' . $port))->asProxy();
        $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));

        return $proxy;
    }

    /**
     * @return string
     */
    abstract protected function baseUrl(): string;
}
