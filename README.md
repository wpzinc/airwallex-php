# Airwallex PHP SDK

An unofficial PHP library for the [Airwallex API](https://www.airwallex.com/docs/api), modelled on Stripe's PHP SDK.

- Covers the full Airwallex API: Payments, Billing, Checkout, Payouts (incl. batch transfers and payers), Spend, Transactional FX, Issuing, Embedded Finance, Lending, Finance, Reconciliation, Accounting, Risk and the sandbox Simulation endpoints (460+ endpoints, see [docs/SERVICES.md](docs/SERVICES.md))
- Handles authentication: exchanges your client ID and API key for an access token, caches it and refreshes it when it expires
- Adds `request_id` idempotency keys automatically on the endpoints that take them
- Retries connection errors, `429`s and `5xx`s with backoff (POSTs are only retried when idempotent)
- Typed exceptions, auto-pagination for both of Airwallex's pagination styles, and webhook signature verification
- No dependencies beyond `ext-curl` and `ext-json`. Bring your own HTTP client or token cache if you want

> This package is not affiliated with or endorsed by Airwallex. "Airwallex" is a trademark of Airwallex Pty Ltd, used here only to describe API compatibility.

## Requirements

PHP 8.2 or later.

## Installation

```bash
composer require wpzinc/airwallex-php
```

## Getting started

Create an API key in the Airwallex web app under **Developer > API keys**.

```php
use WPZinc\Airwallex\AirwallexClient;

$airwallex = new AirwallexClient([
    'client_id'   => getenv('AIRWALLEX_CLIENT_ID'),
    'api_key'     => getenv('AIRWALLEX_API_KEY'),
    'environment' => 'sandbox', // or 'production' (the default)
]);

$intent = $airwallex->paymentIntents->create([
    'amount'            => 49.99,
    'currency'          => 'SGD',
    'merchant_order_id' => 'order_1001',
]);

echo $intent->id;            // property access
echo $intent['client_secret']; // or array access
echo $intent->latest_payment_attempt?->status; // nested objects
```

Services are properties on the client. Resources from the core payments and treasury APIs live at the top level. Other products are grouped by namespace, as in Stripe's SDK:

```php
$airwallex->customers->retrieve('cus_...');
$airwallex->refunds->create(['payment_intent_id' => 'int_...', 'amount' => 10]);
$airwallex->transfers->create([...]);

$airwallex->billing->subscriptions->create([...]);
$airwallex->billing->invoices->finalize('inv_...');
$airwallex->checkout->sessions->create([...]);
$airwallex->fx->quotes->create([...]);
$airwallex->issuing->cards->activate('card_...');
$airwallex->spend->bills->create([...]);
$airwallex->batchTransfers->submit('batch_...');
$airwallex->simulation->payments->createDispute([...]); // sandbox only
```

Every method takes path parameters first, then `$params` (query string for `GET`, JSON body otherwise) and `$opts` (request options). See [docs/SERVICES.md](docs/SERVICES.md) for the full list.

## Billing (subscriptions)

```php
$customer = $airwallex->billing->customers->create([
    'email' => 'jane@example.com',
    'name'  => 'Jane Doe',
]);

$product = $airwallex->billing->products->create(['name' => 'Pro plan']);

$price = $airwallex->billing->prices->create([
    'product_id'    => $product->id,
    'currency'      => 'USD',
    'unit_amount'   => 29,
    'pricing_model' => 'PER_UNIT',
    'recurring'     => ['period' => 1, 'period_unit' => 'MONTH'],
]);

$checkout = $airwallex->billing->checkouts->create([
    'billing_customer_id' => $customer->id,
    'mode'                => 'SUBSCRIPTION',
    'line_items'          => [['price_id' => $price->id, 'quantity' => 1]],
    'success_url'         => 'https://example.com/thanks',
]);
```

Field names follow the Airwallex API reference. The SDK passes `$params` through unchanged, so check the docs for each endpoint's fields.

## Pagination

List methods return a `Collection`. Iterate the current page, or use `autoPagingIterator()` to walk every page lazily. Both of Airwallex's pagination styles are handled: `page_num` with `has_more`, and the `page` cursor with `page_after` / `page_before`.

```php
$page = $airwallex->paymentIntents->all(['status' => 'SUCCEEDED', 'page_size' => 100]);

foreach ($page as $intent) {
    // current page only
}

foreach ($page->autoPagingIterator() as $intent) {
    // every page
}

$next = $page->nextPage();        // null on the last page
$prev = $page->previousPage();
```

Endpoints that return a bare JSON array, such as `balances->current()`, also come back as a `Collection`.

## Request options

```php
$airwallex->balances->current([], [
    'on_behalf_of' => 'acct_...',     // x-on-behalf-of, for connected accounts
    'api_version'  => '2025-02-14',   // override x-api-version for this call
    'headers'      => ['x-custom' => 'value'],
    'timeout'      => 30,
]);
```

## Idempotency

Airwallex uses a `request_id` body field as its idempotency key. The SDK adds a random UUID on the endpoints that accept one, and reuses it when it retries. Pass your own to make retries across processes safe:

```php
$airwallex->transfers->create([
    'request_id' => 'payout-' . $payout->id,
    // ...
]);
```

Airwallex doesn't replay a reused `request_id`. It rejects it with `duplicate_request`, which the SDK throws as `IdempotencyException`. If you get one after a timeout or retry, the original request almost certainly succeeded, so look the resource up (e.g. by `merchant_order_id`) rather than creating it again.

Set `'auto_request_id' => false` in the config to turn this off.

## Errors

API errors throw a subclass of `WPZinc\Airwallex\Exception\ApiErrorException`:

| Exception | Status |
| --- | --- |
| `InvalidRequestException` | 400 |
| `AuthenticationException` | 401 |
| `PermissionException` | 403, or 401 "Insufficient permissions" (scoped key missing a permission) |
| `NotFoundException` | 404 |
| `IdempotencyException` | 400 `duplicate_request` (extends `InvalidRequestException`) |
| `ConflictException` | 409 |
| `RateLimitException` | 429 |
| `ServerException` | 5xx |
| `UnknownApiErrorException` | other |

Network failures throw `ApiConnectionException`. Every SDK exception implements `ExceptionInterface`.

```php
use WPZinc\Airwallex\Exception\ApiErrorException;
use WPZinc\Airwallex\Exception\InvalidRequestException;

try {
    $airwallex->customers->create(['first_name' => 'a']);
} catch (InvalidRequestException $e) {
    $e->getMessage();       // "first_name is too short"
    $e->getAirwallexCode(); // "invalid_argument"
    $e->getSource();        // "first_name"
    $e->getDetails();
    $e->getErrors();        // details.errors, e.g. [['code' => '001', 'source' => 'entity_type']]
    $e->getHttpStatus();    // 400
} catch (ApiErrorException $e) {
    // anything else from the API
}
```

## Webhooks

Airwallex signs each delivery with HMAC-SHA256 over the `x-timestamp` header followed by the raw body. Verify it before trusting the event:

```php
use WPZinc\Airwallex\Exception\SignatureVerificationException;
use WPZinc\Airwallex\Webhook;

$payload = file_get_contents('php://input');

try {
    $event = Webhook::constructEvent(
        $payload,
        $_SERVER['HTTP_X_SIGNATURE'] ?? '',
        $_SERVER['HTTP_X_TIMESTAMP'] ?? '',
        getenv('AIRWALLEX_WEBHOOK_SECRET')
    );
} catch (SignatureVerificationException $e) {
    http_response_code(400);
    exit;
}

match ($event->type()) {
    'payment_intent.succeeded' => handlePayment($event->object()),
    'subscription.created'     => handleSubscription($event->object()),
    default                    => null,
};

http_response_code(200);
```

`Webhook::constructEventFromHeaders($payload, $headers, $secret)` reads the headers from an array instead (e.g. `getallheaders()` or a PSR-7 request). Timestamps older than 5 minutes are rejected; pass a different tolerance in seconds, or `null` to skip the check.

Manage webhook endpoints with `$airwallex->webhooks`.

## Files

Uploads go to Airwallex's separate files host:

```php
$file = $airwallex->files->upload('/path/to/evidence.pdf', ['notes' => 'Delivery receipt']);
$links = $airwallex->files->downloadLinks(['file_ids' => [$file->file_id]]);
```

## Configuration

| Option | Default | Description |
| --- | --- | --- |
| `client_id`, `api_key` | | Your credentials |
| `access_token` | | Use an existing bearer token (e.g. from the Partner API OAuth flow) instead of logging in |
| `environment` | `production` | `production` or `sandbox` |
| `api_version` | `AirwallexClient::API_VERSION` | Sent as `x-api-version`. Set to `null` to use your account's default |
| `login_as` | | `x-login-as` account ID, for scoped keys linked to several accounts |
| `on_behalf_of` | | Default `x-on-behalf-of` connected account ID |
| `max_network_retries` | `2` | Retries for connection errors, 429 and 5xx |
| `timeout` | `80` | Request timeout in seconds |
| `auto_request_id` | `true` | Add `request_id` automatically where needed |
| `http_client` | `CurlClient` | Any `HttpClient\ClientInterface` implementation |
| `token_store` | `InMemoryTokenStore` | Any `Auth\TokenStoreInterface` implementation |
| `base_url`, `files_base_url` | | Override the API hosts |

### Caching access tokens

Access tokens last 30 minutes. The default store only keeps them for the current PHP process, so a web app would otherwise log in on every request. Implement `TokenStoreInterface` against your cache to share tokens:

```php
use WPZinc\Airwallex\Auth\AccessToken;
use WPZinc\Airwallex\Auth\TokenStoreInterface;

final class RedisTokenStore implements TokenStoreInterface
{
    public function __construct(private \Redis $redis) {}

    public function get(string $key): ?AccessToken
    {
        $data = $this->redis->get($key);
        return $data ? AccessToken::fromArray(json_decode($data, true)) : null;
    }

    public function put(string $key, AccessToken $token): void
    {
        $this->redis->setex($key, max(1, $token->expiresAt - time()), json_encode($token->toArray()));
    }

    public function forget(string $key): void
    {
        $this->redis->del($key);
    }
}

$airwallex = new AirwallexClient([..., 'token_store' => new RedisTokenStore($redis)]);
```

### Custom HTTP client

Implement `WPZinc\Airwallex\HttpClient\ClientInterface` (one `request()` method) to send requests through Guzzle, Laravel's HTTP client, or a mock in your tests.

## Endpoints the SDK doesn't wrap yet

```php
$result = $airwallex->rawRequest('post', '/api/v1/some/new_endpoint/create', ['foo' => 'bar'], [], requestId: true);
```

## Development

```bash
composer install
composer test
```

### Sandbox tests

`tests/Sandbox` runs real requests against the Airwallex sandbox: payments, billing checkout, pagination, webhooks, file uploads and errors. Create a scoped sandbox API key, then add a `.env` (git-ignored):

```
AIRWALLEX_CLIENT_ID=...
AIRWALLEX_API_KEY=...
AIRWALLEX_ACCOUNT_ID=...
```

```bash
vendor/bin/phpunit --testsuite sandbox
php tests/Sandbox/sweep.php   # calls every parameterless GET endpoint and prints the result
```

The default `composer test` only runs the offline unit suite.

The service classes, resource classes and [docs/SERVICES.md](docs/SERVICES.md) are generated from [codegen/spec.json](codegen/spec.json). To add or rename an endpoint, edit the spec and run `composer generate`. See [codegen/README.md](codegen/README.md).

## Roadmap

- Laravel package: service provider, config, cache-backed token store, webhook route and middleware
- A Cashier-style billing package for Laravel on top of Airwallex Billing

## License

MIT
