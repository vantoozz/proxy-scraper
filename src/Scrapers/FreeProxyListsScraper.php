<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Generator;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;

/**
 * Class FreeProxyListsScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class FreeProxyListsScraper implements ScraperInterface, Discoverable
{

    private const BASE_URL = 'http://www.freeproxylists.com/';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * FreeProxyListsScraper constructor.
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return Generator|Proxy[]
     * @throws ScraperException
     */
    public function get(): Generator
    {
        foreach ($this->getPages() as $page) {
            yield from $this->crawlPage($page);
        }
    }

    /**
     * @return Generator|string[]
     * @throws ScraperException
     */
    private function getPages(): Generator
    {
        foreach (['elite', 'anonymous', 'non-anonymous', 'https', 'standard', 'socks'] as $type) {
            yield from $this->getPagesOfType($type);
        }
    }

    /**
     * @param string $type
     * @return Generator|string[]
     * @throws ScraperException
     */
    private function getPagesOfType(string $type): Generator
    {
        try {
            $html = $this->httpClient->get(static::BASE_URL . $type . '.html');
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $matches = [];
        preg_match_all('/' . $type . '\/\d+.html/', $html, $matches);

        foreach ($matches[0] as $page) {
            yield 'load_' . str_replace('/', '_', $page);
        }
    }

    /**
     * @param string $page
     * @return Generator
     * @throws ScraperException
     */
    private function crawlPage(string $page): Generator
    {
        try {
            $html = $this->httpClient->get(static::BASE_URL . $page);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $matches = [];

        preg_match_all('/gt;(\d+\.\d+\.\d+\.\d+)&lt;\/td&gt;&lt;td&gt;(\d+)&lt/', $html, $matches);

        foreach (array_keys($matches[1]) as $key) {
            try {
                $proxy = (new ProxyString($matches[1][$key] . ':' . $matches[2][$key]))->asProxy();
            } catch (InvalidArgumentException $e) {
                continue;
            }
            $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));

            yield $proxy;
        }
    }
}
