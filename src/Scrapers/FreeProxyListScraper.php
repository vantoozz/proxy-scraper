<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

/**
 * Class FreeProxyListScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class FreeProxyListScraper extends AbstractFreeProxyListScraper
{
    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return 'https://www.free-proxy-list.net/';
    }
}
