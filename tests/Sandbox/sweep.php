<?php

/**
 * Calls every parameterless GET endpoint against the sandbox and prints the result.
 *
 * Usage: php tests/Sandbox/sweep.php
 */

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use WPZinc\Airwallex\Collection;
use WPZinc\Airwallex\Exception\ApiConnectionException;
use WPZinc\Airwallex\Exception\ApiErrorException;

$spec    = json_decode((string) file_get_contents(dirname(__DIR__, 2) . '/codegen/spec.json'), true);
$clients = ['acct' => sandboxClient(true), 'org' => sandboxClient(false)];

foreach ($spec['services'] as $s) {
    foreach ($s['methods'] as $m) {
        if ($m['http'] !== 'GET' || str_contains($m['path'], '{') || $s['namespace'] === 'Simulation') {
            continue;
        }

        $label = ltrim($s['namespace'] . '.' . $s['property'] . '.' . $m['name'], '.');

        // Try as the account first, then at org level
        foreach ($clients as $who => $client) {
            $factory = $s['namespace'] === '' ? $client : $client->{lcfirst($s['namespace'])};
            try {
                $r    = $factory->{$s['property']}->{$m['name']}();
                $info = $r instanceof Collection
                    ? 'items=' . count($r) . ' meta=' . implode(',', $r->keys())
                    : 'keys=' . implode(',', array_slice($r->keys(), 0, 8));
                printf("%-4s %-4s %-52s %s\n", $who, 'OK', $label, $info);
                continue 2;
            } catch (ApiConnectionException $e) {
                printf("%-4s %-4s %-52s %s\n", $who, 'CONN', $label, substr($e->getMessage(), 0, 100));
            } catch (ApiErrorException $e) {
                printf("%-4s %-4s %-52s %s\n", $who, $e->getHttpStatus(), $label, $e->getAirwallexCode() . ': ' . substr($e->getMessage(), 0, 100));
            }
        }
    }
}
