<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\ProxyListOrgScraper;

/**
 * Class ProxyListOrgScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class ProxyListOrgScraperTest extends IntegrationTest
{

    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new ProxyListOrgScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        self::assertGreaterThanOrEqual(50, count($proxies));
    }
}
