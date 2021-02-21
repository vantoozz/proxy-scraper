<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\ClarketmProxyListScraper;

/**
 * Class ClarketmProxyListScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class ClarketmProxyListScraperTest extends IntegrationTest
{

    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new ClarketmProxyListScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        self::assertGreaterThanOrEqual(100, count($proxies));
    }
}
