<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Scrapers;

/**
 * Class BlogspotProxyScraper
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class BlogspotProxyScraper extends AbstractRssBloggerScraper implements Discoverable
{

    /**
     * @return string
     */
    protected function rssBloggerUrl(): string
    {
        return 'https://blogspotproxy.blogspot.com/feeds/posts/default';
    }
}
