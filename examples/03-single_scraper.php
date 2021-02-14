<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/../vendor/autoload.php';

$scraper = new Scrapers\FreeProxyListScraper(guzzleHttpClient());

foreach ($scraper->get() as $proxy) {
    echo $proxy . "\n";
}
