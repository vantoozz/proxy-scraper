<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
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
     */
    public function get(): \Generator
    {
        foreach (explode("\n", $this->text) as $line) {
            try {
                $proxy = (new ProxyString($line))->asProxy();
            } catch (InvalidArgumentException $e) {
                continue;
            }

            yield $proxy;
        }
    }
}
