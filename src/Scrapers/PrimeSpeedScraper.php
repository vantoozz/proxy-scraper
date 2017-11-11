<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Metric;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Class PrimeSpeedScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class PrimeSpeedScraper implements ScraperInterface
{
    private const URL = 'http://www.prime-speed.ru/proxy/free-proxy-list/all-working-proxies.php';

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
     * @throws \Vantoozz\ProxyScraper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        try {
            $html = $this->httpClient->get(static::URL);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $list = $this->extractList($html);

        foreach ((new TextScraper($list))->get() as $proxy) {
            $proxy->addMetric(new Metric(Metrics::SOURCE, static::class));
            yield $proxy;
        }
    }

    /**
     * @param string $html
     * @return string
     * @throws \Vantoozz\ProxyScraper\Exceptions\ScraperException
     */
    private function extractList(string $html): string
    {
        $expectedPartsCount = 2;

        $parts = explode("&lt;proxy_server_name&gt; : &lt;proxy_port_number&gt;\n\n0.0.0.0:80\n", $html);
        if ($expectedPartsCount !== count($parts)) {
            throw new ScraperException('Unexpected markup');
        }

        $parts = explode("\n\n\n\n</pre>", $parts[1]);
        if ($expectedPartsCount !== count($parts)) {
            throw new ScraperException('Unexpected markup');
        }

        return $parts[0];
    }
}
