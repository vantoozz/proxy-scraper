<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\HttpClient;

use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;

/**
 * Class FailingDummyHttpClient
 * @package Vantoozz\ProxyScraper\HttpClient
 */
final class FailingDummyHttpClient implements HttpClientInterface
{

    /**
     * @var string
     */
    private $message;

    /**
     * FailingDummyHttpClient constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param string $uri
     * @return string
     * @throws HttpClientException
     */
    public function get(string $uri): string
    {
        throw new HttpClientException($this->message);
    }
}
