<?php declare(strict_types = 1);

use Vantoozz\ProxyScraper\Exceptions\ValidationException;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;
use Vantoozz\ProxyScraper\Validators;

require_once __DIR__ . '/../vendor/autoload.php';

$scraper = new class implements Scrapers\ScraperInterface
{
    public function get(): \Generator
    {
        yield new Proxy(new Ipv4('104.202.117.106'), new Port(1234));
        yield new Proxy(new Ipv4('192.168.0.1'), new Port(8888));
    }
};

$validator = new Validators\ValidatorPipeline;
$validator->addStep(new Validators\Ipv4RangeValidator);

foreach ($scraper->get() as $proxy) {

    try {
        $validator->validate($proxy);
        echo '[OK] ' . (string)$proxy . "\n";
    } catch (ValidationException $e) {
        echo '[Error] ' . $e->getMessage() . ': ' . (string)$proxy . "\n";
    }
}
