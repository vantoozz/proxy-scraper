<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class SocksProxyScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class SocksProxyScraper extends AbstractFreeProxyListScraper implements Discoverable
{
    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return 'https://www.socks-proxy.net/';
    }
}
