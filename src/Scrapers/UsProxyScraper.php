<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class UsProxyScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class UsProxyScraper extends AbstractFreeProxyListScraper
{
    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return 'https://www.us-proxy.org/';
    }
}
