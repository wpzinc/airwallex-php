<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

/**
 * Thrown when a webhook signature or timestamp fails verification.
 */
class SignatureVerificationException extends \RuntimeException implements ExceptionInterface
{
}
