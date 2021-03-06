# Proxy Scraper

Library for scraping free proxies lists written in PHP

[![Build Status](https://travis-ci.org/vantoozz/proxy-scraper.svg?branch=master)](https://travis-ci.org/vantoozz/proxy-scraper)
[![Coverage Status](https://coveralls.io/repos/github/vantoozz/proxy-scraper/badge.svg?branch=master)](https://coveralls.io/github/vantoozz/proxy-scraper?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/4b3e0816e98d486e9f0eff445a6310c6)](https://www.codacy.com/app/vantoozz/proxy-scraper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=vantoozz/proxy-scraper&amp;utm_campaign=Badge_Grade)
[![Packagist](https://img.shields.io/packagist/v/vantoozz/proxy-scraper.svg)](https://packagist.org/packages/vantoozz/proxy-scraper)

### Quick start

```bash
composer require vantoozz/proxy-scraper:~3 guzzlehttp/guzzle:~7 guzzlehttp/psr7 hanneskod/classtools
```

```php
<?php declare(strict_types=1);

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

foreach (proxyScraper()->get() as $proxy) {
    echo $proxy . "\n";
}
```

---

### Older versions

This is version 3 of the library. For version 2 please check [v2](https://github.com/vantoozz/proxy-scraper/tree/v2)
branch; for version 1 please check [v1](https://github.com/vantoozz/proxy-scraper/tree/v1)
branch.

#### Upgrade

[How to upgrade](#Upgrade-from-version-2)

### Setup

The library requires a PSR-18 compatible HTTP client. To use the library you have to install any of them, e.g.:

```bash
composer require guzzlehttp/guzzle:~7 guzzlehttp/psr7
```

All available clients are listed on Packagist: https://packagist.org/providers/psr/http-client-implementation.

Then install proxy-scraper library itself:

```bash
composer require vantoozz/proxy-scraper:~3
```

### Usage

#### Auto-configuration

The simplest way to start using the library is to use `proxyScraper()` function which instantiates and configures all
the scrapers.

Please note, auto-configuration function in addition to `guzzlehttp/guzzle:~7` and `guzzlehttp/psr7`
requires `hanneskod/classtools` dependency.

```bash
composer require guzzlehttp/guzzle:~7 guzzlehttp/psr7 hanneskod/classtools
```

```php
<?php declare(strict_types=1);

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

foreach (proxyScraper()->get() as $proxy) {
    echo $proxy . "\n";
}
```

##### HTTP Client

In not using auto-configuration you will need an HTTP client. 

The library provides `guzzleHttpClient()` function creating and configuring the client.

```php
<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;

use function Vantoozz\ProxyScraper\guzzleHttpClient;
use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = guzzleHttpClient();

$scraper = proxyScraper($httpClient);

try {
    echo $scraper->get()->current()->getIpv4(). "\n";
} catch (ScraperException $e) {
    echo $e->getMessage() . "\n";
}
```

You can create own HTTP client by implementing `HttpClientInterface`:

```php
<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/vendor/autoload.php';

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
    echo $scraper->get()->current()->getIpv4(). "\n";
} catch (ScraperException $e) {
    echo $e->getMessage() . "\n";
}
```

Of course, you may manually configure the scraper and underlying HTTP client:

#### Single scraper

```php
<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Scrapers;

use function Vantoozz\ProxyScraper\guzzleHttpClient;

require_once __DIR__ . '/vendor/autoload.php';

$scraper = new Scrapers\UsProxyScraper(guzzleHttpClient());

foreach ($scraper->get() as $proxy) {
    echo $proxy . "\n";
}

```

#### Composite scraper

You can easily get data from many scrapers at once:

```php
<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Scrapers;

use function Vantoozz\ProxyScraper\guzzleHttpClient;

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = guzzleHttpClient();

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
<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/vendor/autoload.php';

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

```

Will output

```
An error occurred: some error
192.168.0.1:8888
```

In the same manner you may configure exceptions handling for the scraper created with `proxyScraper()` function as it
returns an instance of `CompositeScraper`:

```php
<?php declare(strict_types=1);

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

By default, Proxy object has _source_ metric:

```php
<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers;

use function Vantoozz\ProxyScraper\guzzleHttpClient;

require_once __DIR__ . '/vendor/autoload.php';

$scraper = new Scrapers\UsProxyScraper(guzzleHttpClient());

/** @var Proxy $proxy */
$proxy = $scraper->get()->current();

foreach ($proxy->getMetrics() as $metric) {
    echo $metric->getName() . ': ' . $metric->getValue() . "\n";
}
```

Will output

```
source: Vantoozz\ProxyScraper\Scrapers\UsProxyScraper
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

### Upgrade from version 2

The biggest difference from version 2 is the HTTP client configuration.

Instead of

```php
$httpClient = new \Vantoozz\ProxyScraper\HttpClient\Psr18HttpClient(
    new \Http\Adapter\Guzzle6\Client(new \GuzzleHttp\Client([
        'connect_timeout' => 2,
        'timeout' => 3,
    ])),
    new \Http\Message\MessageFactory\GuzzleMessageFactory
);
```

the client should be instantiated like

```php
$httpClient = \Vantoozz\ProxyScraper\guzzleHttpClient();
```
