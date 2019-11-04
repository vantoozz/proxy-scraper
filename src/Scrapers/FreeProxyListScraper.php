<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class FreeProxyListScraper
 * @package Vantoozz\ProxyScraper\Scrapers
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
