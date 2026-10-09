<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * Thrown when the SDK is called with invalid arguments.
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
