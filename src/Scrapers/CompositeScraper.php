<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Generator;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use function call_user_func;
use function is_callable;

/**
 * Class CompositeScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class CompositeScraper implements ScraperInterface
{
    /**
     * @var ScraperInterface[]
     */
    private $scrapers = [];

    /**
     * @var
     */
    private $exceptionHandler;

    /**
     * @return Generator|Proxy[]
     * @throws ScraperException
     */
    public function get(): Generator
    {
        foreach ($this->scrapers as $scraper) {
            try {
                yield from $scraper->get();
            } catch (ScraperException $e) {
                $this->handleScraperException($e);
            }
        }
    }

    /**
     * @param ScraperInterface $scraper
     * @return void
     */
    public function addScraper(ScraperInterface $scraper): void
    {
        $this->scrapers[] = $scraper;
    }

    /**
     * @param callable $exceptionHandler
     * @return void
     */
    public function handleScraperExceptionWith(callable $exceptionHandler): void
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * @param ScraperException $e
     * @return void
     * @throws ScraperException
     */
    private function handleScraperException(ScraperException $e): void
    {
        if (!is_callable($this->exceptionHandler)) {
            throw $e;
        }
        call_user_func($this->exceptionHandler, $e);
    }
}
