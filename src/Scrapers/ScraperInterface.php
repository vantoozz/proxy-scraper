<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Generator;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Interface ScraperInterface
 * @package Vantoozz\ProxyScraper\Scrapers
 */
interface ScraperInterface
{
    /**
     * @return Generator|Proxy[]
     * @throws ScraperException
     */
    public function get(): Generator;
}
