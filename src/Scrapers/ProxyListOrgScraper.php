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
 * Class ProxyListOrgScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class ProxyListOrgScraper implements ScraperInterface, Discoverable
{
    /**
     *
     */
    private const BASE_URL = 'http://proxy-list.org/english/index.php?p=';
    /**
     *
     */
    private const LAST_PAGE = 10;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * ProxyListOrgScraper constructor.
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
        $page = 0;
        while (++$page <= self::LAST_PAGE) {
            yield from $this->crawlPage($page);
            if ($page !== self::LAST_PAGE) {
                usleep(150000);
            }
        }
    }

    /**
     * @param int $page
     * @return Generator|Proxy[]
     * @throws ScraperException
     */
    private function crawlPage(int $page): Generator
    {
        try {
            $html = $this->httpClient->get(self::BASE_URL . $page);
        } catch (HttpClientException $e) {
            throw new ScraperException($e->getMessage(), $e->getCode(), $e);
        }

        $matches = [];

        preg_match_all('/javascript\">Proxy\(\'(.+)\'\)<\/script/', $html, $matches);

        foreach ($matches[1] as $match) {
            try {
                $proxy = (new ProxyString(base64_decode($match)))->asProxy();
            } catch (InvalidArgumentException $e) {
                continue;
            }

            $proxy->addMetric(new Metric(Metrics::SOURCE, self::class));

            yield $proxy;
        }
    }
}
