<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * HTTP 401 - the access token is invalid.
 */
class AuthenticationException extends ApiErrorException
{
}
