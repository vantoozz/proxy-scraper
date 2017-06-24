<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\Scrapers;

/**
 * Class SpysMeScraper
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class SpysMeScraper extends RemoteTextScraper
{
    /**
     * @return string
     */
    protected function remoteTextUrl(): string
    {
        return 'http://spys.me/proxy.txt';
    }
}
