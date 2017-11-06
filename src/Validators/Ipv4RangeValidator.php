<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Validators;

use Vantoozz\ProxyScraper\Exceptions\ValidationException;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Class Ipv4RangeValidator
 * @package Vantoozz\ProxyScraper\Filters
 */
final class Ipv4RangeValidator implements ValidatorInterface
{
    /**
     * @param Proxy $proxy
     * @return void
     * @throws ValidationException
     */
    public function validate(Proxy $proxy): void
    {
        if (!filter_var(
            (string)$proxy->getIpv4(),
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        )
        ) {
            throw new ValidationException('IPv4 is in private range');
        }
    }
}
