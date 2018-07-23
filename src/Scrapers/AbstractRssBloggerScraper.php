<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;
use Vantoozz\ProxyScraper\Text;

/**
 * Class AbstractRssBloggerScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
abstract class AbstractRssBloggerScraper implements ScraperInterface
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
     * @throws ScraperException
     * @throws \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    public function get(): \Generator
    {
        try {
            $html = $this->httpClient->get($this->rssBloggerUrl());
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        if (!(new Text($html))->isXml()) {
            throw new ScraperException('Invalid XML');
        }

        $feed = simplexml_load_string($html);
        yield from $this->fetchFeed($feed);
    }

    /**
     * @param \SimpleXMLElement $feed
     * @return \Generator
     * @throws \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    private function fetchFeed(\SimpleXMLElement $feed)
    {
        foreach ($feed->entry as $entry) {
            preg_match_all('/\d+\.\d+\.\d+\.\d+:\d{1,5}/m', (string)$entry->content, $matches);
            foreach ($matches[0] as $proxyString) {
                try {
                    $proxy = (new ProxyString($proxyString))->asProxy();
                    $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));
                    yield $proxy;
                } catch (\Exception $e) {
                    throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
                }
            }
        }
    }

    /**
     * @return string
     */
    abstract protected function rssBloggerUrl(): string;
}
