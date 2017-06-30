<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

use Vantoozz\ProxyScraper\Proxy;

/**
 * Interface ScraperInterface
 * @package Vantoozz\ProxyScraper\Scrapers
 */
interface ScraperInterface
{
    /**
     * @return \Generator|Proxy[]
     * @throws \Vantoozz\ProxyScraper\Exceptions\ScraperException
     */
    public function get(): \Generator;
}
