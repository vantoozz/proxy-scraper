<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Vantoozz\ProxyScraper\Exceptions\ProxyScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;

/**
 * Class TextScraper
 * @package Vantoozz\ProxyScraper\Scrapers
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
     * @throws \Vantoozz\ProxyScraper\Exceptions\ScraperException
     */
    public function get(): \Generator
    {
        foreach (explode("\n", $this->text) as $line) {
            try {
                yield (new ProxyString($line))->asProxy();
            } catch (ProxyScraperException $e) {
                continue;
            }
        }
    }
}
