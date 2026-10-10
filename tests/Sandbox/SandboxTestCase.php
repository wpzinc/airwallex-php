<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests\Sandbox;

use PHPUnit\Framework\TestCase;
use WPZinc\Airwallex\AirwallexClient;

require_once __DIR__ . '/bootstrap.php';

/**
 * Runs against the real Airwallex sandbox. Needs AIRWALLEX_CLIENT_ID, AIRWALLEX_API_KEY
 * and AIRWALLEX_ACCOUNT_ID in the environment or .env.
 *
 * Run with: vendor/bin/phpunit --testsuite sandbox
 */
abstract class SandboxTestCase extends TestCase
{
    protected AirwallexClient $airwallex;

    protected function setUp(): void
    {
        if (! getenv('AIRWALLEX_CLIENT_ID') || ! getenv('AIRWALLEX_API_KEY')) {
            $this->markTestSkipped('Sandbox credentials not set');
        }

        $this->airwallex = sandboxClient();
    }

    protected function uniqueId(string $prefix): string
    {
        return $prefix . '-' . bin2hex(random_bytes(6));
    }
}
