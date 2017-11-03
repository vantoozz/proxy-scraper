<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper;

use Vantoozz\ProxyScraper\Enums\Anonymity;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\AppraiserException;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;

/**
 * Class ProxyAppraiser
 * @package Vantoozz\ProxyScraper\Appraisers
 * @deprecated
 */
final class Appraiser
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $whoamiHost;

    /**
     * @var Ipv4
     */
    private $realIp;

    /**
     * ProxyAppraiser constructor.
     * @param HttpClientInterface $httpClient
     * @param string $whoamiHost
     */
    public function __construct(HttpClientInterface $httpClient, string $whoamiHost)
    {
        $this->httpClient = $httpClient;
        $this->whoamiHost = $whoamiHost;
    }

    /**
     * @param Proxy $proxy
     * @return \Generator|Metric[]
     * @throws AppraiserException
     */
    public function appraise(Proxy $proxy): \Generator
    {
        if (!$this->realIp) {
            $this->realIp = $this->getRealIp();
        }

        try {
            $json = $this->httpClient->getProxied('http://' . $this->whoamiHost, (string)$proxy);
        } catch (HttpClientException $e) {
            yield new Metric(Metrics::AVAILABLE, false);
            return;
        }

        try {
            $data = $this->decodeResponse($json);
        } catch (AppraiserException $e) {
            yield new Metric(Metrics::AVAILABLE, false);
            return;
        }

        yield new Metric(Metrics::ANONYMITY, $this->makeAnonymity($data));
        yield new Metric(Metrics::AVAILABLE, true);

        try {
            $this->decodeResponse($this->httpClient->getProxied('https://' . $this->whoamiHost, (string)$proxy));
            yield new Metric(Metrics::HTTPS, true);
        } catch (HttpClientException | AppraiserException $e) {
            yield new Metric(Metrics::HTTPS, false);
        }
    }

    /**
     * @param array $data
     * @return string
     */
    private function makeAnonymity(array $data): string
    {
        if ((string)$this->realIp === $data['remote_address']) {
            return Anonymity::TRANSPARENT;
        }
        /** @var array $headers */
        $headers = $data['headers'];
        foreach ($headers as $header) {
            if ((string)$this->realIp === $header) {
                return Anonymity::TRANSPARENT;
            }
        }

        if (array_intersect(array_keys($headers), $this->proxySignatures())) {
            return Anonymity::ANONYMOUS;
        }

        return Anonymity::ELITE;
    }

    /**
     * @return Ipv4
     * @throws AppraiserException
     */
    private function getRealIp(): Ipv4
    {
        try {
            $json = $this->httpClient->get('http://' . $this->whoamiHost);
        } catch (HttpClientException $e) {
            throw new AppraiserException($e->getMessage(), $e->getCode(), $e);
        }
        $data = $this->decodeResponse($json);

        try {
            return new Ipv4($data['remote_address']);
        } catch (InvalidArgumentException $e) {
            throw new AppraiserException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $json
     * @return array
     * @throws AppraiserException
     */
    private function decodeResponse(string $json): array
    {
        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new AppraiserException(json_last_error_msg());
        }

        foreach (['remote_address', 'headers'] as $field) {
            if (!isset($data[$field])) {
                throw new AppraiserException('Bad response from whoami service');
            }
        }
        if (!is_string($data['remote_address'])) {
            throw new AppraiserException('Bad response from whoami service');
        }

        if (!is_array($data['headers'])) {
            throw new AppraiserException('Bad response from whoami service');
        }

        return $data;
    }

    /**
     * @return array
     */
    private function proxySignatures(): array
    {
        return [
            'Client-Ip',
            'Forwarded',
            'Forwarded-For',
            'Forwarded-For-Ip',
            'Proxy-Connection',
            'Via',
            'X-Forwarded-For',
            'X-Forwarded',
            'X-Proxy-Id',
            'X-Real-Ip',
            'Xroxy-Connection',
        ];
    }
}
