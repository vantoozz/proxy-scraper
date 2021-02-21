<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers\Decorators;

use Generator;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class Timed
 * @package Vantoozz\ProxyScraper\Scrapers\Decorators
 */
final class Timed implements ScraperInterface
{
    public const EVENT_DONE = 'done';
    public const EVENT_PROXY_FOUND = 'proxy_found';

    /**
     * @var ScraperInterface
     */
    private $scraper;

    /**
     * @var Generator
     */
    private $output;

    /**
     * Timed constructor.
     * @param ScraperInterface $scraper
     * @param Generator $output
     */
    public function __construct(ScraperInterface $scraper, Generator $output)
    {
        $this->scraper = $scraper;
        $this->output = $output;
    }

    /**
     * @return Generator
     * @throws ScraperException
     */
    public function get(): Generator
    {
        $startTime = microtime(true);

        foreach ($this->scraper->get() as $proxy) {
            $iterationStartTime = microtime(true);
            yield $proxy;
            $this->output->send([self::EVENT_PROXY_FOUND, microtime(true) - $iterationStartTime]);
        }

        $this->output->send([self::EVENT_DONE, microtime(true) - $startTime]);
    }
}
