<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new GuzzleAdapter(new GuzzleClient),
    new GuzzleMessageFactory
);

$compositeScraper = new Scrapers\CompositeScraper;

// Throws an exception
$compositeScraper->addScraper(new class implements Scrapers\ScraperInterface
{
    public function get(): \Generator
    {
        throw new ScraperException('some error');
    }
});

// No exceptions
$compositeScraper->addScraper(new class implements Scrapers\ScraperInterface
{
    public function get(): \Generator
    {
        yield new Proxy(new Ipv4('127.0.0.1'), new Port(8888));
    }
});

// Set exception handler
$compositeScraper->handleScraperExceptionWith(function (ScraperException $e) {
    echo 'An error occurs: ' . $e->getMessage() . "\n";
});

foreach ($compositeScraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
