<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests;

use WPZinc\Airwallex\AirwallexClient;
use WPZinc\Airwallex\Auth\AccessToken;
use WPZinc\Airwallex\Auth\InMemoryTokenStore;
use WPZinc\Airwallex\Exception\ApiConnectionException;
use WPZinc\Airwallex\Exception\AuthenticationException;
use WPZinc\Airwallex\Exception\InvalidArgumentException;
use WPZinc\Airwallex\Exception\InvalidRequestException;
use WPZinc\Airwallex\Exception\NotFoundException;
use WPZinc\Airwallex\Exception\RateLimitException;
use WPZinc\Airwallex\Exception\ServerException;
use WPZinc\Airwallex\Resource\PaymentIntent;

final class ApiRequestorTest extends TestCase
{
    public function testLogsInOnceAndReusesToken(): void
    {
        $this->http->queue(200, ['id' => 'int_1'])->queue(200, ['id' => 'int_2']);

        $this->client->paymentIntents->retrieve('int_1');
        $this->client->paymentIntents->retrieve('int_2');

        $this->assertSame(1, $this->http->logins);
        $login = $this->http->requests[0];
        $this->assertSame('https://api.sandbox.airwallex.com/api/v1/authentication/login', $login['url']);
        $this->assertSame('client_123', $login['headers']['x-client-id']);
        $this->assertSame('key_456', $login['headers']['x-api-key']);
        $this->assertArrayNotHasKey('x-login-as', $login['headers']);
        $this->assertSame('Bearer tok_1', $this->http->last()['headers']['Authorization']);
    }

    public function testLoginAsHeader(): void
    {
        $client = $this->makeClient(['login_as' => 'acct_9']);
        $client->balances->current();

        $this->assertSame('acct_9', $this->http->requests[0]['headers']['x-login-as']);
    }

    public function testExpiredTokenTriggersNewLogin(): void
    {
        $store = new InMemoryTokenStore();
        $client = $this->makeClient(['token_store' => $store]);
        $client->balances->current();

        // Replace the cached token with one about to expire.
        $key = 'airwallex_token_' . sha1('https://api.sandbox.airwallex.com|client_123|');
        $this->assertNotNull($store->get($key));
        $store->put($key, new AccessToken('old', time() + 30));

        $client->balances->current();
        $this->assertSame(2, $this->http->logins);
        $this->assertSame('Bearer tok_1_2', $this->http->last()['headers']['Authorization']);
    }

    public function testRefreshesTokenOnceOn401(): void
    {
        $this->http->queue(401, ['code' => 'unauthorized'])->queue(200, ['id' => 'int_1']);

        $intent = $this->client->paymentIntents->retrieve('int_1');

        $this->assertSame('int_1', $intent->id);
        $this->assertSame(2, $this->http->logins);
        $this->assertSame('Bearer tok_1_2', $this->http->last()['headers']['Authorization']);
    }

    public function testPersistent401Throws(): void
    {
        $this->http->queue(401, ['code' => 'unauthorized', 'message' => 'bad token'])->queue(401, ['code' => 'unauthorized', 'message' => 'bad token']);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('bad token');
        $this->client->paymentIntents->retrieve('int_1');
    }

    public function testStaticAccessTokenSkipsLogin(): void
    {
        $client = new AirwallexClient(['access_token' => 'partner_tok', 'http_client' => $this->http]);
        $client->balances->current();

        $this->assertSame(0, $this->http->logins);
        $this->assertSame('Bearer partner_tok', $this->http->last()['headers']['Authorization']);
        $this->assertStringStartsWith('https://api.airwallex.com/', $this->http->last()['url']);
    }

    public function testAddsRequestIdWhereEndpointNeedsIt(): void
    {
        $this->client->paymentIntents->create(['amount' => 10, 'currency' => 'USD']);
        $body = $this->http->lastBody();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $body['request_id']);
        $this->assertSame(10, $body['amount']);
    }

    public function testKeepsCallerRequestId(): void
    {
        $this->client->paymentIntents->create(['request_id' => 'mine']);

        $this->assertSame('mine', $this->http->lastBody()['request_id']);
    }

    public function testNoRequestIdWhereEndpointDoesNotTakeOne(): void
    {
        $this->client->issuing->cards->activate('card_1');

        $this->assertSame('{}', $this->http->last()['body']);
    }

    public function testAutoRequestIdCanBeDisabled(): void
    {
        $client = $this->makeClient(['auto_request_id' => false]);
        $client->paymentIntents->create(['amount' => 1]);

        $this->assertArrayNotHasKey('request_id', $this->http->lastBody());
    }

    public function testRetriesWithSameRequestId(): void
    {
        $this->http->queue(500)->queueConnectionError()->queue(201, ['id' => 'int_1']);

        $intent = $this->client->paymentIntents->create(['amount' => 1]);

        $this->assertSame('int_1', $intent->id);
        $requests = $this->http->apiRequests();
        $this->assertCount(3, $requests);
        $ids = array_map(fn ($r) => json_decode($r['body'], true)['request_id'], $requests);
        $this->assertCount(1, array_unique($ids));
    }

    public function testDoesNotRetryNonIdempotentPostOn500(): void
    {
        $this->http->queue(500, ['code' => 'internal_error'])->queue(200);

        try {
            $this->client->issuing->cards->activate('card_1');
            $this->fail('Expected ServerException');
        } catch (ServerException $e) {
            $this->assertSame(500, $e->getHttpStatus());
        }
        $this->assertCount(1, $this->http->apiRequests());
    }

    public function testDoesNotRetryNonIdempotentPostOnConnectionError(): void
    {
        $this->http->queueConnectionError();

        $this->expectException(ApiConnectionException::class);
        $this->client->issuing->cards->activate('card_1');
    }

    public function testRetries429EvenWithoutRequestId(): void
    {
        $this->http->queue(429, [], ['Retry-After' => '1'])->queue(200, ['id' => 'card_1']);

        $this->assertSame('card_1', $this->client->issuing->cards->activate('card_1')->id);
        $this->assertCount(2, $this->http->apiRequests());
    }

    public function testGivesUpAfterMaxRetries(): void
    {
        $client = $this->makeClient(['max_network_retries' => 1]);
        $this->http->queue(429)->queue(429)->queue(200);

        $this->expectException(RateLimitException::class);
        try {
            $client->balances->current();
        } finally {
            $this->assertCount(2, $this->http->apiRequests());
        }
    }

    public function testErrorMapping(): void
    {
        $this->http->queue(400, ['code' => 'invalid_argument', 'source' => 'first_name', 'message' => 'first_name is too short', 'details' => ['min' => 2]]);

        try {
            $this->client->customers->create(['first_name' => 'a']);
            $this->fail('Expected InvalidRequestException');
        } catch (InvalidRequestException $e) {
            $this->assertSame('first_name is too short', $e->getMessage());
            $this->assertSame('invalid_argument', $e->getAirwallexCode());
            $this->assertSame('first_name', $e->getSource());
            $this->assertSame(['min' => 2], $e->getDetails());
            $this->assertSame(400, $e->getHttpStatus());
        }
    }

    public function testNotFoundWithEmptyBody(): void
    {
        $this->http->queue(404, '');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('HTTP 404');
        $this->client->refunds->retrieve('nope');
    }

    public function testHeaders(): void
    {
        $this->client->balances->current();
        $headers = $this->http->last()['headers'];

        $this->assertSame(AirwallexClient::API_VERSION, $headers['x-api-version']);
        $this->assertArrayNotHasKey('x-on-behalf-of', $headers);
        $this->assertStringStartsWith('wpzinc-airwallex-php/', $headers['User-Agent']);
    }

    public function testRequestOptions(): void
    {
        $this->client->balances->current([], ['on_behalf_of' => 'acct_1', 'api_version' => null, 'headers' => ['X-Test' => 'yes']]);
        $headers = $this->http->last()['headers'];

        $this->assertSame('acct_1', $headers['x-on-behalf-of']);
        $this->assertArrayNotHasKey('x-api-version', $headers);
        $this->assertSame('yes', $headers['X-Test']);
    }

    public function testDefaultOnBehalfOfAndApiVersionConfig(): void
    {
        $client = $this->makeClient(['on_behalf_of' => 'acct_2', 'api_version' => '2024-08-07']);
        $client->balances->current();
        $headers = $this->http->last()['headers'];

        $this->assertSame('acct_2', $headers['x-on-behalf-of']);
        $this->assertSame('2024-08-07', $headers['x-api-version']);
    }

    public function testUnknownOptionThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->balances->current([], ['amount' => 1]);
    }

    public function testQueryEncoding(): void
    {
        $this->client->paymentIntents->all(['status' => 'SUCCEEDED', 'page_size' => 50, 'flag' => true, 'skip' => null]);

        $this->assertSame('https://api.sandbox.airwallex.com/api/v1/pa/payment_intents?status=SUCCEEDED&page_size=50&flag=true', $this->http->last()['url']);
        $this->assertNull($this->http->last()['body']);
    }

    public function testPathParamsAreEncoded(): void
    {
        $this->client->paymentIntents->retrieve('a/b c');

        $this->assertStringEndsWith('/api/v1/pa/payment_intents/a%2Fb%20c', $this->http->last()['url']);
    }

    public function testEmptyPathParamThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->paymentIntents->retrieve('');
    }

    public function testReturnsTypedResource(): void
    {
        $this->http->queue(200, ['id' => 'int_1', 'amount' => 12.5, 'latest_payment_attempt' => ['id' => 'att_1', 'status' => 'AUTHORIZED']]);

        $intent = $this->client->paymentIntents->retrieve('int_1');

        $this->assertInstanceOf(PaymentIntent::class, $intent);
        $this->assertSame(12.5, $intent->amount);
        $this->assertSame('AUTHORIZED', $intent->latest_payment_attempt->status);
        $this->assertSame('att_1', $intent['latest_payment_attempt']['id']);
        $this->assertSame(200, $intent->getLastResponse()->status);
    }

    public function testConfigValidation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AirwallexClient(['client_id' => 'x']);
    }

    public function testUnknownConfigThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AirwallexClient(['client_id' => 'x', 'api_key' => 'y', 'enviroment' => 'sandbox']);
    }

    public function testRawRequest(): void
    {
        $this->http->queue(200, ['ok' => true]);

        $result = $this->client->rawRequest('post', '/api/v1/something/new', ['a' => 1], [], true);

        $this->assertTrue($result->ok);
        $this->assertArrayHasKey('request_id', $this->http->lastBody());
    }

    public function testFileUploadUsesFilesHostAndMultipart(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'awx');
        file_put_contents($tmp, 'evidence');
        $this->http->queue(201, ['file_id' => 'file_1']);

        $file = $this->client->files->upload($tmp, ['notes' => 'receipt']);
        unlink($tmp);

        $req = $this->http->last();
        $this->assertSame('file_1', $file->file_id);
        $this->assertSame('https://files.sandbox.airwallex.com/api/v1/files/upload', $req['url']);
        $this->assertInstanceOf(\CURLFile::class, $req['body']['file']);
        $this->assertSame('receipt', $req['body']['notes']);
        $this->assertArrayNotHasKey('Content-Type', $req['headers']);
    }
}
