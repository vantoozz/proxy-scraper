<?php declare(strict_types=1);


namespace Vantoozz\ProxyScraper\Scrapers;

use Generator;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;

/**
 * Class Distinct
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class Distinct implements ScraperInterface
{

    /**
     * @var ScraperInterface
     */
    private $scraper;

    /**
     * Distinct constructor.
     * @param ScraperInterface $scraper
     */
    public function __construct(ScraperInterface $scraper)
    {
        $this->scraper = $scraper;
    }

    /**
     * @return Generator
     * @throws ScraperException
     */
    public function get(): Generator
    {
        $proxies = [];
        foreach ($this->scraper->get() as $proxy) {
            $proxies[(string)$proxy] = $proxy;
        }
        yield from array_values($proxies);
    }
}
