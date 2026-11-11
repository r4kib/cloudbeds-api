<?php

namespace R4kib\Cloudbeds\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use R4kib\Cloudbeds\Cloudbeds;
use R4kib\Cloudbeds\Exceptions\CloudbedsAPIException;
use R4kib\Cloudbeds\Exceptions\CloudbedsHttpException;
use R4kib\Cloudbeds\Provider\CloudbedsProvider;

class CloudbedsTest extends TestCase
{
    private Cloudbeds $cloudbeds;

    protected function setUp(): void
    {
        $this->cloudbeds = new Cloudbeds(['clientId' => 'id', 'clientSecret' => 'secret', 'redirectUri' => 'https://example.com']);
    }

    public function testOAuthHelperAndDefaultVersion(): void
    {
        $this->assertInstanceOf(CloudbedsProvider::class, $this->cloudbeds->getOauthHelper());
        $this->assertSame('https://hotels.cloudbeds.com/api/v1.1', $this->cloudbeds->getOptions()['apiBaseUrl']);
        $this->assertInstanceOf(Client::class, $this->cloudbeds->getHttpClient());
    }

    public function testRequestsSendTokenAndParameters(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler(array_fill(0, 4, new Response(200, [], '{"success":true}'))));
        $stack->push(Middleware::history($history));
        $this->cloudbeds->setHttpClient(new Client(['handler' => $stack]));
        $token = new AccessToken(['access_token' => 'test-token']);

        foreach (['get', 'post', 'put', 'delete'] as $method) {
            $this->assertSame(['success' => true], $this->cloudbeds->$method('/test', $token, ['key' => 'value']));
        }

        foreach ($history as $index => $entry) {
            $request = $entry['request'];
            $this->assertSame(strtoupper(['get', 'post', 'put', 'delete'][$index]), $request->getMethod());
            $this->assertSame('Bearer test-token', $request->getHeaderLine('Authorization'));
            $this->assertSame('key=value', $index === 0 ? $request->getUri()->getQuery() : (string) $request->getBody());
        }
    }

    public function testApiErrorsAreReported(): void
    {
        $this->cloudbeds->setHttpClient(new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], '{"error":true,"message":"failure"}'),
        ]))]));
        $this->expectException(CloudbedsAPIException::class);
        $this->cloudbeds->get('/test', new AccessToken(['access_token' => 'token']));
    }

    public function testHttpErrorsAreReported(): void
    {
        $this->cloudbeds->setHttpClient(new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(500),
        ]))]));
        $this->expectException(CloudbedsHttpException::class);
        $this->cloudbeds->get('/test', new AccessToken(['access_token' => 'token']));
    }
}
