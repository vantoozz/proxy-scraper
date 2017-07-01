<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\SystemTests\ProxiesMiner;

use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class ScrapersProxiesMiner
 * @package Vantoozz\ProxyScraper\SystemTests
 */
final class ScrapersProxiesMiner implements ProxiesMinerInterface
{
    /**
     * @var ScraperInterface[]
     */
    private $scrapers = [];

    /**
     * @param ScraperInterface $scraper
     */
    public function addScraper(ScraperInterface $scraper): void
    {
        $fullClass = explode('\\', get_class($scraper));
        $class = end($fullClass);
        $this->scrapers[$class] = $scraper;
    }

    /**
     * @return array
     */
    public function getProxies(): array
    {
        $proxies = [];
        foreach ($this->scrapers as $class => $scraper) {
            try {
                $proxies[$class] = $this->fetchProxies($scraper);
            } catch (ScraperException $e) {
                $proxies[$class] = [];
            }
        }
        return $proxies;
    }

    /**
     * @param ScraperInterface $scraper
     * @return array
     * @throws ScraperException
     */
    private function fetchProxies(ScraperInterface $scraper): array
    {
        $proxies = [];
        foreach ($scraper->get() as $proxy) {
            $parts = explode(':', (string)$proxy);
            if (!isset($proxies[ip2long($parts[0])])) {
                $proxies[ip2long($parts[0])] = [];
            }
            $proxies[ip2long($parts[0])][(int)$parts[1]] = (string)$proxy;
        }
        ksort($proxies);
        return $proxies;
    }
}