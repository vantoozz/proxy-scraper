<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/../vendor/autoload.php';

$compositeScraper = new Scrapers\CompositeScraper;

// Set exception handler
$compositeScraper->handleScraperExceptionWith(function (ScraperException $e) {
    echo 'An error occurred: ' . $e->getMessage() . "\n";
});

// Fake scraper throwing an exception
$compositeScraper->addScraper(new class implements Scrapers\ScraperInterface {
    public function get(): Generator
    {
        throw new ScraperException('some error');
    }
});

// Fake scraper with no exceptions
$compositeScraper->addScraper(new class implements Scrapers\ScraperInterface {
    public function get(): Generator
    {
        yield new Proxy(new Ipv4('192.168.0.1'), new Port(8888));
    }
});

//Run composite scraper
foreach ($compositeScraper->get() as $proxy) {
    echo $proxy . "\n";
}
