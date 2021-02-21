<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;

use function Vantoozz\ProxyScraper\guzzleHttpClient;
use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = guzzleHttpClient();

$scraper = proxyScraper($httpClient);

try {
    echo $scraper->get()->current()->getIpv4() . "\n";
} catch (ScraperException $e) {
    echo $e->getMessage() . "\n";
}
