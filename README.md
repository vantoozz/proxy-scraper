# Free proxies scraper 
Library for scraping free proxies lists

[![Build Status](https://travis-ci.org/vantoozz/proxy-scraper.svg?branch=master)](https://travis-ci.org/vantoozz/proxy-scraper)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/4b3e0816e98d486e9f0eff445a6310c6)](https://www.codacy.com/app/vantoozz/proxy-scraper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=vantoozz/proxy-scraper&amp;utm_campaign=Badge_Grade)
[![Coverage Status](https://coveralls.io/repos/github/vantoozz/proxy-scraper/badge.svg?branch=master)](https://coveralls.io/github/vantoozz/proxy-scraper?branch=master)


### Setup

Proxy-scrapper library is built on top of [HTTPlug](http://httplug.io/) and requires a compatible HTTP client. Available clients are listed on Packagist: https://packagist.org/providers/php-http/client-implementation. To use the library you have to install any of them, e.g.:

```bash
composer require php-http/guzzle6-adapter
```

Then install proxy-scrapper library itself:
```bash
composer require vantoozz/proxy-scraper
```


### Tests

##### Unit tests
```bash
./vendor/bin/phpunit --testsuite=unit
```

##### Integration tests
```bash
./vendor/bin/phpunit --testsuite=integration
```


[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d5cffc7f-030f-49b3-ac7f-3769db037ee7/big.png)](https://insight.sensiolabs.com/projects/d5cffc7f-030f-49b3-ac7f-3769db037ee7)