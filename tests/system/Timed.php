<?php declare(strict_types=1);


namespace Vantoozz\ProxyScraper\SystemTests;


use Generator;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class Timed
 * @package Vantoozz\ProxyScraper\SystemTests
 */
final class Timed implements ScraperInterface
{

    /**
     * @var ScraperInterface
     */
    private $scraper;

    /**
     * Timed constructor.
     * @param ScraperInterface $scraper
     */
    public function __construct(ScraperInterface $scraper)
    {
        $this->scraper = $scraper;
    }

    public function get(): Generator
    {
        $startTime = microtime(true);

        $proxies = iterator_to_array($this->scraper->get(), false);

        $fullClass = explode('\\', get_class($this->scraper));
        $class = end($fullClass);
        echo $class . ' => ' . round(microtime(true) - $startTime, 2) . "s\n";

        yield from $proxies;
    }
}
