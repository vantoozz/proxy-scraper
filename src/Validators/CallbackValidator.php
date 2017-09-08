<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\Validators;

use Vantoozz\ProxyScraper\Exceptions\ValidationException;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Class CallbackValidator
 * @package Vantoozz\ProxyScraper\Validators
 */
final class CallbackValidator implements ValidatorInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * CallbackValidator constructor.
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Proxy $proxy
     * @throws ValidationException
     */
    public function validate(Proxy $proxy): void
    {
        call_user_func($this->callback, $proxy);
    }
}
