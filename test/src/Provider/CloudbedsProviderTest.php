<?php

namespace R4kib\Cloudbeds\Test\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use R4kib\Cloudbeds\Provider\CloudbedsProvider;
use R4kib\Cloudbeds\Provider\CLoudbedsResourceOwner;

class CloudbedsProviderTest extends TestCase
{
    private CloudbedsProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new CloudbedsProvider([
            'clientId' => 'id', 'clientSecret' => 'secret', 'redirectUri' => 'https://example.com',
            'apiBaseUrl' => 'https://hotels.cloudbeds.com/api/v1.1',
        ]);
    }

    public function testAuthorizationAndEndpointUrls(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $this->assertSame('/api/v1.1/oauth', parse_url($url, PHP_URL_PATH));
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $this->assertSame('id', $query['client_id']);
        $this->assertSame('code', $query['response_type']);
        $this->assertSame('/api/v1.1/access_token', parse_url($this->provider->getBaseAccessTokenUrl([]), PHP_URL_PATH));
        $this->assertSame('/api/v1.1/userinfo', parse_url($this->provider->getResourceOwnerDetailsUrl(new AccessToken(['access_token' => 'token'])), PHP_URL_PATH));
    }

    public function testTokenAndResourceOwner(): void
    {
        $this->provider->setHttpClient(new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"access_token":"token","refresh_token":"refresh","expires_in":3600}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"user_id":"user","first_name":"First"}'),
        ]))]));
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'code']);
        $this->assertSame('token', $token->getToken());
        $this->assertSame('refresh', $token->getRefreshToken());
        $owner = $this->provider->getResourceOwner($token);
        $this->assertInstanceOf(CLoudbedsResourceOwner::class, $owner);
        $this->assertSame('user', $owner->getId());
        $this->assertSame('First', $owner->getFirstName());
    }
}
