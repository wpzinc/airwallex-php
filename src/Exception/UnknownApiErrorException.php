<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * Any other non-2xx HTTP response.
 */
class UnknownApiErrorException extends ApiErrorException
{
}
