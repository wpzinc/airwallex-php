<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * HTTP 5xx - an error on Airwallex's side.
 */
class ServerException extends ApiErrorException
{
}
