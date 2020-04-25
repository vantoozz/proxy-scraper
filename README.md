# Proxy Scraper 
Library for scraping free proxies lists written in PHP


[![Build Status](https://travis-ci.org/vantoozz/proxy-scraper.svg?branch=master)](https://travis-ci.org/vantoozz/proxy-scraper)
[![Coverage Status](https://coveralls.io/repos/github/vantoozz/proxy-scraper/badge.svg?branch=master)](https://coveralls.io/github/vantoozz/proxy-scraper?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/4b3e0816e98d486e9f0eff445a6310c6)](https://www.codacy.com/app/vantoozz/proxy-scraper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=vantoozz/proxy-scraper&amp;utm_campaign=Badge_Grade)
[![Packagist](https://img.shields.io/packagist/v/vantoozz/proxy-scraper.svg)](https://packagist.org/packages/vantoozz/proxy-scraper)

### Quick start
```bash
composer require vantoozz/proxy-scraper php-http/guzzle6-adapter hanneskod/classtools
```
```php
<?php declare(strict_types = 1);

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

foreach (proxyScraper()->get() as $proxy) {
    echo $proxy . "\n";
}
```

### Older versions
This is version 2 of the library. For version 1 please check [v1](https://github.com/vantoozz/proxy-scraper/tree/v1) branch.

### Setup

The library uses [HTTPlug](http://httplug.io/) and requires a compatible HTTP client. 
To use the library you have to install any of them, e.g.:

```bash
composer require php-http/guzzle6-adapter
```
All available clients are listed on Packagist: https://packagist.org/providers/php-http/client-implementation.

Then install proxy-scraper library itself:
```bash
composer require vantoozz/proxy-scraper
```

### Usage

#### Auto-configuration
The simplest way to start using the library is to use `proxyScraper()` function 
which instantiates and configures all the scrapers. 

Please note, auto-configuration function in addition to `php-http/guzzle6-adapter` 
requires `hanneskod/classtools` dependency.
```bash
composer require php-http/guzzle6-adapter hanneskod/classtools
```
```php
<?php declare(strict_types = 1);

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

foreach (proxyScraper()->get() as $proxy) {
    echo $proxy . "\n";
}
```

##### HTTP Client
You can override default parameters of the HTTP client like this:
```php
<?php declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new HttpAdapter(new GuzzleClient([
        'connect_timeout' => 2,
        'timeout' => 3,
    ])),
    new MessageFactory
);

foreach (proxyScraper($httpClient)->get() as $proxy) {
    echo $proxy . "\n";
}

```

Of course, you may manually configure the scraper and underlying HTTP client:

#### Single scraper
```php
<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new HttpAdapter(new GuzzleClient([
        'connect_timeout' => 2,
        'timeout' => 3,
    ])),
    new MessageFactory
);

$scraper = new Scrapers\FreeProxyListScraper($httpClient);

foreach ($scraper->get() as $proxy) {
    echo $proxy . "\n";
}
```

#### Composite scraper
You can easily get data from many scrapers at once:
```php
<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new HttpAdapter(new GuzzleClient([
        'connect_timeout' => 2,
        'timeout' => 3,
    ])),
    new MessageFactory
);

$compositeScraper = new Scrapers\CompositeScraper;

$compositeScraper->addScraper(new Scrapers\FreeProxyListScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\CoolProxyScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\SocksProxyScraper($httpClient));

foreach ($compositeScraper->get() as $proxy) {
    echo $proxy . "\n";
}
```

#### Error handling
Sometimes things go wrong. This example shows how to handle errors while getting data from many scrapers:
```php
<?php declare(strict_types = 1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/vendor/autoload.php';

$compositeScraper = new Scrapers\CompositeScraper;

// Set exception handler
$compositeScraper->handleScraperExceptionWith(function (ScraperException $e) {
    echo 'An error occurs: ' . $e->getMessage() . "\n";
});

// Fake scraper throwing an exception
$compositeScraper->addScraper(new class implements Scrapers\ScraperInterface
{
    public function get(): \Generator
    {
        throw new ScraperException('some error');
    }
});

// Fake scraper with no exceptions
$compositeScraper->addScraper(new class implements Scrapers\ScraperInterface
{
    public function get(): \Generator
    {
        yield new Proxy(new Ipv4('192.168.0.1'), new Port(8888));
    }
});

//Run composite scraper
foreach ($compositeScraper->get() as $proxy) {
    echo $proxy . "\n";
}
```
Will output
```
An error occurs: some error
192.168.0.1:8888
```

In the same manner you may configure exceptions handling for the scraper 
created with `proxyScraper()` function as it returns an instance of `CompositeScraper`:
```php
<?php declare(strict_types = 1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

$scraper = proxyScraper();

$scraper->handleScraperExceptionWith(function (ScraperException $e) {
    echo 'An error occurs: ' . $e->getMessage() . "\n";
});
```

#### Validating proxies
Validation steps may be added:
```php
<?php declare(strict_types = 1);

use Vantoozz\ProxyScraper\Exceptions\ValidationException;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;
use Vantoozz\ProxyScraper\Validators;

require_once __DIR__ . '/vendor/autoload.php';

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
        echo '[OK] ' . $proxy . "\n";
    } catch (ValidationException $e) {
        echo '[Error] ' . $e->getMessage() . ': ' . $proxy . "\n";
    }
}
```
Will output
```
[OK] 104.202.117.106:1234
[Error] IPv4 is in private range: 192.168.0.1:8888
```

#### Metrics
A Proxy object may have metrics (metadata) associated with.

By default Proxy object has _source_ metric:
```php
<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new HttpAdapter(new GuzzleClient([
        'connect_timeout' => 2,
        'timeout' => 3,
    ])),
    new MessageFactory
);

$scraper = new Scrapers\FreeProxyListScraper($httpClient);

/** @var Proxy $proxy */
$proxy = $scraper->get()->current();

foreach ($proxy->getMetrics() as $metric) {
    echo $metric->getName() . ': ' . $metric->getValue() . "\n";
}
```
Will output
```
source: Vantoozz\ProxyScraper\Scrapers\FreeProxyListScraper
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
