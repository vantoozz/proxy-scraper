<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = new class implements HttpClientInterface {
    /**
     * @param string $uri
     * @return string
     */
    public function get(string $uri): string
    {
        return "some string";
    }
};

$scraper = proxyScraper($httpClient);

try {
    $scraper->get()->current();
} catch (ScraperException $e) {
    echo $e->getMessage() . "\n";
}
