<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class ProxyServerlistScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class ProxyServerlistScraper extends AbstractRssBloggerScraper implements Discoverable
{

    /**
     * @return string
     */
    protected function rssBloggerUrl(): string
    {
        return 'http://www.proxyserverlist24.top/feeds/posts/default';
    }
}
