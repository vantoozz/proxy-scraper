<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScrapper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScrapper\Scrapers\ProxyDbScraper;

/**
 * Class ProxyDbScraperTest
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class ProxyDbScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new ProxyDbScraper($this->httpClient());

        $scrapperGenerator = $scrapper->get();

        $proxiesCount = 0;
        while (100 > $proxiesCount && $scrapperGenerator->valid()) {
            $scrapperGenerator->next();
            $proxiesCount++;
        }

        $this->assertSame(100, $proxiesCount);
    }
}
