<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

use Symfony\Component\DomCrawler\Crawler as Dom;
use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;
use Vantoozz\ProxyScrapper\Exceptions\ScraperException;
use Vantoozz\ProxyScrapper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScrapper\Ipv4;
use Vantoozz\ProxyScrapper\Port;
use Vantoozz\ProxyScrapper\Proxy;

/**
 * Class AbstractFreeProxyListScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
abstract class AbstractFreeProxyListScraper
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
     * @return \Generator|Proxy[]
     * @throws \RuntimeException if the CssSelector Component is not available
     * @throws \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        try {
            $html = $this->httpClient->get($this->baseUrl(), []);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $rows = (new Dom($html))->filter('#proxylisttable tbody tr');

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
     * @throws \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    private function makeProxy(Dom $row): Proxy
    {
        $ipv4 = $row->filter('td')->eq(0)->text();
        $port = (int)$row->filter('td')->eq(1)->text();

        return new Proxy(new Ipv4($ipv4), new Port($port));
    }

    /**
     * @return string
     */
    abstract protected function baseUrl(): string;
}
