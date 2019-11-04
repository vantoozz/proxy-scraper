<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Generator;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use function is_array;

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
    private const URL = 'https://www.hide-my-ip.com/%s/proxylist.shtml';

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
     * @throws ScraperException
     */
    public function get(): Generator
    {
        try {
            $html = $this->httpClient->get($this->makeUrl());
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
     * @return string
     */
    private function makeUrl(): string
    {
        $languages = [
            'es',
            'fr',
            'it',
            'pt',
            'nl',
            'de',
            'se',
            'dk',
            'pl',
            'tr',
            'ar',
            'ru',
            'ro',
            'cn',
            'kr',
            'jp',
            'vn',
            'th',
            'sr',

        ];

        return sprintf(static::URL, $languages[array_rand($languages)]);
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
        $json = trim(explode(";", $parts[1])[0]);
        $data = json_decode($json, true);
        if (!$data) {
            throw new ScraperException('Cannot parse json: ' . json_last_error_msg());
        }
        return $data;
    }

    /**
     * @param array $item
     * @return Proxy
     * @throws InvalidArgumentException
     */
    private function makeProxy(array $item): Proxy
    {
        if (!isset($item['i'], $item['p'])) {
            throw new InvalidArgumentException('Bad data');
        }

        $proxy = new Proxy(new Ipv4((string)$item['i']), new Port((int)$item['p']));
        $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));

        return $proxy;
    }
}
