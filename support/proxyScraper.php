<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper;

use GuzzleHttp\Client as GuzzleClient;
use hanneskod\classtools\Iterator\ClassIterator;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\HttpClient\Psr18HttpClient;
use Vantoozz\ProxyScraper\Scrapers\Discoverable;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * @param HttpClientInterface|null $httpClient
 * @return Scrapers\CompositeScraper
 */
function proxyScraper(HttpClientInterface $httpClient = null): Scrapers\CompositeScraper
{
    return (new class($httpClient) {
        /**
         * @var HttpClientInterface
         */
        private $httpClient;

        /**
         *
         * @param HttpClientInterface|null $httpClient
         */
        public function __construct(HttpClientInterface $httpClient = null)
        {
            $this->httpClient = $httpClient ?: $this->guzzleHttpClient();
        }

        /**
         * @return Scrapers\CompositeScraper
         */
        public function run(): Scrapers\CompositeScraper
        {
            $compositeScraper = new Scrapers\CompositeScraper;

            $classIterator = new ClassIterator((new Finder)->in(__DIR__ . '/../src/Scrapers'));

            foreach ($classIterator->type(Discoverable::class) as $class) {
                $this->applyClass($class, $compositeScraper);
            }

            return $compositeScraper;
        }

        /**
         * @param ReflectionClass $class
         * @param Scrapers\CompositeScraper $compositeScraper
         */
        private function applyClass(ReflectionClass $class, Scrapers\CompositeScraper $compositeScraper): void
        {
            if (!$class->isInstantiable()) {
                return;
            }

            $parameters = $class->getConstructor()->getParameters();

            if (1 !== count($parameters)) {
                return;
            }

            if (!$parameters[0]->getClass()->implementsInterface(HttpClientInterface::class)) {
                return;
            }

            /** @var ScraperInterface $scraper */
            $scraper = $class->newInstance($this->httpClient);

            $compositeScraper->addScraper($scraper);
        }

        /**
         * @return Psr18HttpClient
         */
        private function guzzleHttpClient(): Psr18HttpClient
        {
            return new Psr18HttpClient(
                new HttpAdapter(new GuzzleClient([
                    'connect_timeout' => 5,
                    'timeout' => 10,
                ])),
                new MessageFactory
            );
        }

    })->run();
}
