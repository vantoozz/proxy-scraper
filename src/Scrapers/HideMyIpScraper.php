<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Class HideMyIpScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class HideMyIpScraper implements ScraperInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     *
     */
    private const URL = 'https://www.hide-my-ip.com/proxylist.shtml';

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
     * @throws \Vantoozz\ProxyScraper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        try {
            $html = $this->httpClient->get(
                static::URL,
                [
                    'User-Agent' =>
                        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) ' .
                        'Chrome/41.0.2228.0 Safari/537.36',
                ]
            );
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($this->extractData($html) as $item) {
            if (!is_array($item)) {
                continue;
            }
            try {
                yield $this->makeProxy($item);
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }
    }

    /**
     * @param string $html
     * @return array
     * @throws ScraperException
     */
    private function extractData(string $html): array
    {
        $expectedPartsCount = 2;
        $parts = explode('var json =', $html, $expectedPartsCount);
        if ($expectedPartsCount !== count($parts)) {
            throw new ScraperException('Unknown markup');
        }
        $json = trim(explode(';<!-- proxylist -->', $parts[1])[0]);
        $data = json_decode($json, true);
        if (!$data) {
            throw new ScraperException('Cannot parse json: ' . json_last_error_msg());
        }
        return $data;
    }

    /**
     * @param array $item
     * @return Proxy
     * @throws \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    private function makeProxy(array $item): Proxy
    {
        if (!isset($item['i'], $item['p'])) {
            throw new InvalidArgumentException('Bad data');
        }

        return new Proxy(new Ipv4((string)$item['i']), new Port((int)$item['p']));
    }
}
