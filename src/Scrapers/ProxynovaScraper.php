<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Generator;
use Symfony\Component\DomCrawler\Crawler as Dom;
use Throwable;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;

/**
 * Class ProxynovaScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class ProxynovaScraper implements ScraperInterface
{
    /**
     *
     */
    private const URL = 'https://www.proxynova.com/proxy-server-list/';
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * ProxynovaScraper constructor.
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
            $html = $this->httpClient->get(self::URL);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $rows = (new Dom($html))->filter('#tbl_proxy_list tbody tr');

        foreach ($rows as $row) {
            try {
                yield $this->makeProxy(new Dom($row));
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }
    }

    /**
     * @param Dom $row
     * @return Proxy
     * @throws InvalidArgumentException
     * @throws ScraperException
     */
    private function makeProxy(Dom $row): Proxy
    {
        try {
            $encodedIp4v = trim($row->filter('td')->eq(0)->text());
            $port = (int)$row->filter('td')->eq(1)->text();
        } catch (Throwable $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        $parts = explode("'", $encodedIp4v);

        $expectedPartsCount = 3;
        if ($expectedPartsCount !== count($parts)) {
            throw new ScraperException('Unknown markup');
        }

        $proxy = (new ProxyString($parts[1] . ':' . $port))->asProxy();
        $proxy->addMetric(new Metric(Metrics::SOURCE, self::class));

        return $proxy;
    }
}
