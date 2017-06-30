<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class MultiproxyScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class MultiproxyScraper extends RemoteTextScraper
{
    /**
     * @return string
     */
    protected function remoteTextUrl(): string
    {
        return 'http://multiproxy.org/txt_all/proxy.txt';
    }
}
