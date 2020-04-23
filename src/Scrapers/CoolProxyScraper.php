<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Generator;
use RuntimeException;
use Throwable;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Class CoolProxyScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class CoolProxyScraper implements ScraperInterface, Discoverable
{
    private const JSON_URL = 'https://www.cool-proxy.net/proxies.json';

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
     * @throws RuntimeException
     * @throws ScraperException
     */
    public function get(): Generator
    {
        try {
            $json = $this->httpClient->get(sprintf(static::JSON_URL));
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $data = json_decode($json, true);
        if (!$data) {
            throw new ScraperException('Cannot parse json: ' . json_last_error_msg());
        }

        if (!is_array($data)) {
            throw new ScraperException('No data');
        }

        foreach ($data as $item) {
            if (!is_array($item)) {
                $item = [];
            }
            try {
                yield $this->makeProxy($item);
            } catch (Throwable $e) {
                continue;
            }
        }
    }


    /**
     * @param array $item
     * @return Proxy
     * @throws Throwable
     */
    private function makeProxy(array $item): Proxy
    {
        if (!isset($item['ip'])) {
            throw new InvalidArgumentException('No IP given');
        }
        if (!isset($item['port'])) {
            throw new InvalidArgumentException('No port given');
        }

        $proxy = new Proxy(new Ipv4($item['ip']), new Port((int)$item['port']));
        $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));

        return $proxy;
    }
}
