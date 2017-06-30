<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class FoxToolsScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class FoxToolsScraper extends RemoteTextScraper
{
    /**
     * @return string
     */
    protected function remoteTextUrl(): string
    {
        return 'http://api.foxtools.ru/v2/Proxy.txt';
    }
}
