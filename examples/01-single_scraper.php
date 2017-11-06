<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Vantoozz\ProxyScraper\HttpClient\GuzzleHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = new GuzzleHttpClient(new GuzzleClient([
    'connect_timeout' => 2,
    'timeout' => 3,
]));
$scraper = new Scrapers\SpysMeScraper($httpClient);

foreach ($scraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
