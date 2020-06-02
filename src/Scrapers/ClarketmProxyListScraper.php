<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class ClarketmProxyListScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class ClarketmProxyListScraper extends RemoteTextScraper implements Discoverable
{
    /**
     * @return string
     */
    protected function remoteTextUrl(): string
    {
        return 'https://raw.githubusercontent.com/clarketm/proxy-list/master/proxy-list-raw.txt';
    }
}
