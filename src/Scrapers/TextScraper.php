<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

use Vantoozz\ProxyScrapper\Exceptions\ProxyScrapperException;
use Vantoozz\ProxyScrapper\Exceptions\RuntimeException;
use Vantoozz\ProxyScrapper\Ipv4;
use Vantoozz\ProxyScrapper\Port;
use Vantoozz\ProxyScrapper\Proxy;

/**
 * Class TextScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class TextScraper implements ScraperInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * TextScraper constructor.
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return \Generator|Proxy[]
     * @throws \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        foreach (explode("\n", $this->text) as $line) {
            try {
                yield $this->makeProxy($line);
            } catch (ProxyScrapperException $e) {
                continue;
            }
        }
    }

    /**
     * @param string $proxy
     * @return Proxy
     * @throws \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     * @throws \Vantoozz\ProxyScrapper\Exceptions\RuntimeException
     */
    private function makeProxy(string $proxy): Proxy
    {
        $expectedPartsCount = 2;
        $parts = explode(':', $proxy);
        if ($expectedPartsCount !== count($parts)) {
            throw new RuntimeException('Bad formatted proxy');
        }

        [$ipv4, $port] = $parts;

        return new Proxy(new Ipv4($ipv4), new Port((int)$port));
    }
}
