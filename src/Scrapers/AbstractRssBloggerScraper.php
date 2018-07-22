<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\ProxyString;

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
     * @return \Generator
     * @throws ScraperException
     * @throws \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    public function get(): \Generator
    {
        try {
            $html = $this->httpClient->get($this->rssBloggerUrl());
            $feed = simplexml_load_string($html);
            foreach ($feed->entry->content as $listContent) {
                preg_match_all('/\d+\.\d+\.\d+\.\d+:\d{1,5}/m', (string)$listContent, $matches);
                foreach ($matches[0] as $proxyString) {
                    yield (new ProxyString($proxyString))->asProxy();
                }
            }
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    abstract protected function rssBloggerUrl(): string;
}
