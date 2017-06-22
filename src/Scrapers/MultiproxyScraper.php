<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;
use Vantoozz\ProxyScrapper\Exceptions\ProxyScrapperException;
use Vantoozz\ProxyScrapper\Exceptions\RuntimeException;
use Vantoozz\ProxyScrapper\Exceptions\ScraperException;
use Vantoozz\ProxyScrapper\HttpClient;
use Vantoozz\ProxyScrapper\Ipv4;
use Vantoozz\ProxyScrapper\Port;
use Vantoozz\ProxyScrapper\Proxy;

/**
 * Class MultiproxyScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class MultiproxyScraper implements ScraperInterface
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     *
     */
    private const PROXIES_LIST_URL = 'http://multiproxy.org/txt_all/proxy.txt';

    /**
     * FreeProxyListScraper constructor.
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return \Generator|Proxy[]
     * @throws \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        try {
            $lines = $this->httpClient->get(static::PROXIES_LIST_URL);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        foreach (explode("\n", $lines) as $line) {
            try {
                yield $this->makeProxy($line);
            } catch (ProxyScrapperException $e) {
                continue;
            }
        }
    }

    /**
     * @param string $proxy
     * @return Proxy
     * @throws \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     * @throws \Vantoozz\ProxyScrapper\Exceptions\RuntimeException
     */
    private function makeProxy(string $proxy): Proxy
    {
        $parts = explode(':', $proxy);
        if (2 !== count($parts)) {
            throw new RuntimeException('Bad formatted proxy');
        }

        [$ip, $port] = $parts;

        return new Proxy(new Ipv4($ip), new Port((int)$port));
    }
}
