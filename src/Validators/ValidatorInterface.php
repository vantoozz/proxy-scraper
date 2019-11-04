<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\Validators;

use Vantoozz\ProxyScraper\Exceptions\ValidationException;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Interface ValidatorInterface
 * @package Vantoozz\ProxyScraper\Filters
 */
interface ValidatorInterface
{
    /**
     * @param Proxy $proxy
     * @throws ValidationException
     */
    public function validate(Proxy $proxy): void;
}
