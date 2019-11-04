<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class SslProxiesScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class SslProxiesScraper extends AbstractFreeProxyListScraper
{
    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return 'https://www.sslproxies.org/';
    }
}
