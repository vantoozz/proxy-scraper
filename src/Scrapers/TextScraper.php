<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

use Vantoozz\ProxyScrapper\Exceptions\ProxyScrapperException;
use Vantoozz\ProxyScrapper\Proxy;
use Vantoozz\ProxyScrapper\ProxyString;

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
                yield (new ProxyString($line))->asProxy();
            } catch (ProxyScrapperException $e) {
                continue;
            }
        }
    }
}
