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
 * Class FreeProxyListScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class FreeProxyListScraper implements ScraperInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     *
     */
    private const BASE_URL = 'https://www.free-proxy-list.net/';

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
        try {
            $html = $this->httpClient->get(static::BASE_URL, []);
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
     * @param Dom $tr
     * @return Proxy
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    private function makeProxy(Dom $tr): Proxy
    {
        $ip = $tr->filter('td')->eq(0)->text();
        $port = (int)$tr->filter('td')->eq(1)->text();

        return new Proxy(new Ipv4($ip), new Port($port));
    }
}
