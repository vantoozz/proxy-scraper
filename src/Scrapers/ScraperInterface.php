<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

use Vantoozz\ProxyScrapper\Proxy;

/**
 * Interface ScraperInterface
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
interface ScraperInterface
{
    /**
     * @return \Generator|Proxy[]
     * @throws \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     */
    public function get(): \Generator;
}
