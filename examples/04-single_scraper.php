<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Scrapers;

use function Vantoozz\ProxyScraper\guzzleHttpClient;

require_once __DIR__ . '/../vendor/autoload.php';

$scraper = new Scrapers\UsProxyScraper(guzzleHttpClient());

foreach ($scraper->get() as $proxy) {
    echo $proxy . "\n";
}
