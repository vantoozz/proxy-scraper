<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Appraiser;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\DummyHtmlClient;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;

final class AppraiserTest extends TestCase
{
    /**
     * @test
     * @dataProvider metricsDataProvider
     * @param array $proxied
     * @param array $expected
     * @throws \Vantoozz\ProxyScraper\Exceptions\AppraiserException
     */
    public function it_returns_metrics(array $proxied, array $expected): void
    {
        $client = new DummyHtmlClient(
            json_encode(['remote_address' => '127.0.0.1', 'headers' => []]),
            json_encode($proxied)
        );
        $appraiser = new Appraiser($client, 'some url');

        $metrics = [];
        foreach ($appraiser->appraise(new Proxy(new Ipv4('8.8.8.8'), new Port(8888))) as $metric) {
            $metrics[$metric->getName()] = $metric->getValue();
        }
        ksort($expected);
        ksort($metrics);

        static::assertSame($expected, $metrics);
    }

    /**
     * @test
     */
    public function it_validates_response_from_whoami(): void
    {
        $client = new DummyHtmlClient(
            json_encode(['remote_address' => '127.0.0.1', 'headers' => []]),
            'some string'
        );
        $appraiser = new Appraiser($client, 'some url');

        $expected = ['Available' => 0];

        $metrics = [];
        foreach ($appraiser->appraise(new Proxy(new Ipv4('8.8.8.8'), new Port(8888))) as $metric) {
            $metrics[$metric->getName()] = $metric->getValue();
        }
        ksort($expected);
        ksort($metrics);

        static::assertSame($expected, $metrics);
    }

    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\AppraiserException
     * @expectedExceptionMessage Invalid ipv4 string: some string
     */
    public function it_throws_exception_on_bad_whoami_response(): void
    {
        $client = new DummyHtmlClient(
            json_encode(['remote_address' => 'some string', 'headers' => []]),
            'some string'
        );
        $appraiser = new Appraiser($client, 'some url');

        $appraiser->appraise(new Proxy(new Ipv4('8.8.8.8'), new Port(8888)))->current();
    }

    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\AppraiserException
     * @expectedExceptionMessage error message
     */
    public function it_throws_exception_on_http_client_exception(): void
    {
        $client = new class implements HttpClientInterface
        {
            public function get(string $uri): string
            {
                throw new HttpClientException('error message');
            }

            public function getProxied(string $uri, string $proxy): string
            {
                return '';
            }
        };
        $appraiser = new Appraiser($client, 'some url');

        $appraiser->appraise(new Proxy(new Ipv4('8.8.8.8'), new Port(8888)))->current();
    }

    /**
     * @test
     */
    public function it_returns_metric_on_proxied_http_client_exception(): void
    {
        $client = new class implements HttpClientInterface
        {
            public function get(string $uri): string
            {
                return json_encode(['remote_address' => '127.0.0.1', 'headers' => []]);
            }

            public function getProxied(string $uri, string $proxy): string
            {
                throw new HttpClientException('error message');
            }
        };

        $appraiser = new Appraiser($client, 'some url');

        $expected = ['Available' => 0];

        $metrics = [];
        foreach ($appraiser->appraise(new Proxy(new Ipv4('8.8.8.8'), new Port(8888))) as $metric) {
            $metrics[$metric->getName()] = $metric->getValue();
        }
        ksort($expected);
        ksort($metrics);

        static::assertSame($expected, $metrics);
    }

    /**
     * @return array
     */
    public function metricsDataProvider(): array
    {
        return [
            [
                ['remote_address' => '127.0.0.1', 'headers' => []],
                ['Available' => 1, 'Anonymity' => 'Transparent'],
            ],
            [
                ['remote_address' => '127.0.0.2', 'headers' => []],
                ['Available' => 1, 'Anonymity' => 'Elite'],
            ],
            [
                ['remote_address' => '127.0.0.2', 'headers' => ['X-Real-Ip' => '127.0.0.1']],
                ['Available' => 1, 'Anonymity' => 'Transparent'],
            ],
            [
                ['remote_address' => '127.0.0.2', 'headers' => ['X-Real-Ip' => '127.0.0.3']],
                ['Available' => 1, 'Anonymity' => 'Anonymous'],
            ],
            [
                [],
                ['Available' => 0],
            ],
            [
                ['remote_address' => 123, 'headers' => 123],
                ['Available' => 0],
            ],
            [
                ['remote_address' => '127.0.0.1', 'headers' => 123],
                ['Available' => 0],
            ],
        ];
    }
}
