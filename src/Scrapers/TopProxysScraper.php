<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class TopProxysScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class TopProxysScraper extends AbstractRssBloggerScraper implements Discoverable
{

    /**
     * @return string
     */
    protected function rssBloggerUrl(): string
    {
        return 'https://topproxys.blogspot.com/feeds/posts/default';
    }
}
