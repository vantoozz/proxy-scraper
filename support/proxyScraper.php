<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper;

use hanneskod\classtools\Iterator\ClassIterator;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
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
            $this->httpClient = $httpClient ?: guzzleHttpClient();
        }

        /**
         * @return Scrapers\CompositeScraper
         */
        public function build(): Scrapers\CompositeScraper
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

            $constructor = $class->getConstructor();

            if(null === $constructor){
                return;
            }

            $parameters = $constructor->getParameters();

            if (1 !== count($parameters)) {
                return;
            }

            $type = $parameters[0]->getType();

            if(null === $type){
                return;
            }

            if (!$type instanceof HttpClientInterface) {
                return;
            }

            /** @var ScraperInterface $scraper */
            $scraper = $class->newInstance($this->httpClient);

            $compositeScraper->addScraper($scraper);
        }
    })->build();
}
