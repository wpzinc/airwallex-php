<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use WPZinc\Airwallex\AirwallexClient;

// Load .env into the environment
$env = dirname(__DIR__, 2) . '/.env';
if (is_file($env)) {
    foreach (file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_contains($line, '=') && ! str_starts_with(trim($line), '#')) {
            [$k, $v] = explode('=', $line, 2);
            putenv(trim($k) . '=' . trim($v, " \"'"));
        }
    }
}

function sandboxClient(bool $asAccount = true): AirwallexClient
{
    return new AirwallexClient([
        'client_id'   => getenv('AIRWALLEX_CLIENT_ID'),
        'api_key'     => getenv('AIRWALLEX_API_KEY'),
        'login_as'    => $asAccount ? (getenv('AIRWALLEX_ACCOUNT_ID') ?: null) : null,
        'environment' => 'sandbox',
        'timeout'     => (int) (getenv('AIRWALLEX_TIMEOUT') ?: 30),
        'max_network_retries' => (int) (getenv('AIRWALLEX_RETRIES') ?: 1),
    ]);
}
