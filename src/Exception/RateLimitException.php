<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * HTTP 429 - too many requests.
 */
class RateLimitException extends ApiErrorException
{
}
