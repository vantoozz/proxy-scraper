# Proxy Scraper 
Library for scraping free proxies lists written in PHP

[![Build Status](https://travis-ci.org/vantoozz/proxy-scraper.svg?branch=master)](https://travis-ci.org/vantoozz/proxy-scraper)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/4b3e0816e98d486e9f0eff445a6310c6)](https://www.codacy.com/app/vantoozz/proxy-scraper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=vantoozz/proxy-scraper&amp;utm_campaign=Badge_Grade)
[![Coverage Status](https://coveralls.io/repos/github/vantoozz/proxy-scraper/badge.svg?branch=master)](https://coveralls.io/github/vantoozz/proxy-scraper?branch=master)


### Setup

Proxy-scraper library is built on top of [HTTPlug](http://httplug.io/) and requires a compatible HTTP client. Available clients are listed on Packagist: https://packagist.org/providers/php-http/client-implementation. To use the library you have to install any of them, e.g.:

```bash
composer require php-http/guzzle6-adapter
```

Then install proxy-scraper library itself:
```bash
composer require vantoozz/proxy-scraper
```

### Usage

#### Single scraper
```php
<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new GuzzleAdapter(new GuzzleClient),
    new GuzzleMessageFactory
);

$scraper = new Scrapers\FreeProxyListScraper($httpClient);

foreach ($scraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
```

#### Composite scraper
You can easily get data from many scrapers at once:
```php
<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new GuzzleAdapter(new GuzzleClient),
    new GuzzleMessageFactory
);

$compositeScraper = new Scrapers\CompositeScraper;

$compositeScraper->addScraper(new Scrapers\FreeProxyListScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\MultiproxyScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\ProxyDbScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\SocksProxyScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\SpysMeScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\UsProxyScraper($httpClient));

foreach ($compositeScraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
```

#### Error handling
Sometimes things go wrong. This example shows how to handle errors while getting data from many scrapers:
```php
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

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new GuzzleAdapter(new GuzzleClient),
    new GuzzleMessageFactory
);

$compositeScraper = new Scrapers\CompositeScraper;

// Set exception handler
$compositeScraper->handleScraperExceptionWith(function (ScraperException $e) {
    echo 'An error occurs: ' . $e->getMessage() . "\n";
});

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
        yield new Proxy(new Ipv4('192.168.0.1'), new Port(8888));
    }
});

//Run scraper
foreach ($compositeScraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
```
Will output
```
An error occurs: some error
192.168.0.1:8888
```

_Note. Examples use Guzzle as HTTP client._


### Testing

##### Unit tests
```bash
./vendor/bin/phpunit --testsuite=unit
```

##### Integration tests
```bash
./vendor/bin/phpunit --testsuite=integration
```

##### System tests
```bash
php ./tests/systemTests.php
```


[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d5cffc7f-030f-49b3-ac7f-3769db037ee7/big.png)](https://insight.sensiolabs.com/projects/d5cffc7f-030f-49b3-ac7f-3769db037ee7)
