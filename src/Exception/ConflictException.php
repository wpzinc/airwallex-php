<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * HTTP 409 - the request conflicts with another request.
 */
class ConflictException extends ApiErrorException
{
}
