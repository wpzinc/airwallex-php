<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * HTTP 403 - missing token or insufficient permissions.
 */
class PermissionException extends ApiErrorException
{
}
