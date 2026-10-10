<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * HTTP 400 duplicate_request - the request_id has already been used.
 *
 * Airwallex rejects a reused request_id instead of replaying the original response. If this follows
 * a timeout or retry, the original request most likely succeeded, so look the resource up instead.
 */
class IdempotencyException extends InvalidRequestException
{
}
