<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

/**
 * Class SocksProxyScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class SocksProxyScraper extends AbstractFreeProxyListScraper
{
    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return 'https://www.socks-proxy.net/';
    }
}
