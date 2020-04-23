<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper;

use GuzzleHttp\Client as GuzzleClient;
use hanneskod\classtools\Iterator\ClassIterator;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\Finder\Finder;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Scrapers\Discoverable;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class Factory
 * @package Vantoozz\ProxyScraper
 */
final class ScrapersBuilder
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @return Scrapers\CompositeScraper
     */
    public function auto(): Scrapers\CompositeScraper
    {

        $compositeScraper = new Scrapers\CompositeScraper;

        $classIterator = new ClassIterator((new Finder)->in(__DIR__ . '/Scrapers'));

        foreach ($classIterator->type(Discoverable::class)->where('isInstantiable') as $class) {
            /** @var ReflectionClass $class */
            /** @var ReflectionParameter[] $parameters */
            $parameters = $class->getConstructor()->getParameters();
            if (1 !== count($parameters)) {
                continue;
            }

            if (!$parameters[0]->getClass()->implementsInterface(HttpClientInterface::class)) {
                continue;
            }

            /** @var ScraperInterface $scraper */
            $scraper = $class->newInstance($this->httpClient());

            $compositeScraper->addScraper($scraper);
        }

        return $compositeScraper;
    }

    /**
     * @return HttplugHttpClient
     */
    private function httpClient(): HttplugHttpClient
    {
        if (!$this->httpClient instanceof HttpClientInterface) {
            $this->httpClient = new HttplugHttpClient(
                new HttpAdapter(new GuzzleClient([
                    'connect_timeout' => 5,
                    'timeout' => 10,
                ])),
                new MessageFactory
            );
        }

        return $this->httpClient;
    }
}
