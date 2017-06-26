<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;
use Vantoozz\ProxyScrapper\Exceptions\ScraperException;
use Vantoozz\ProxyScrapper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScrapper\Proxy;

/**
 * Class RemoteTextScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
abstract class RemoteTextScraper
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
     * @throws \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        try {
            $text = $this->httpClient->get($this->remoteTextUrl(), []);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        yield from (new TextScraper($text))->get();
    }

    /**
     * @return string
     */
    abstract protected function remoteTextUrl(): string;
}
