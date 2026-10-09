<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * Thrown when the SDK can't reach Airwallex (DNS, TLS, timeouts etc).
 */
class ApiConnectionException extends \RuntimeException implements ExceptionInterface
{
}
