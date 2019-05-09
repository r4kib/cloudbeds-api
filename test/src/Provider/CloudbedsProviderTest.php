<?php
/**
 * Created by PhpStorm.
 * User: rakib
 * Date: 06-May-19
 * Time: 2:58 PM
 */

namespace R4kib\Cloudbeds\Test\Provider\CloudbedsTest;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use R4kib\Cloudbeds\Provider\CloudbedsProvider;
use Mockery as m;
use R4kib\Cloudbeds\Provider\CLoudbedsResourceOwner;

class CloudbedsProviderTest extends TestCase
{
    protected $provider;

    protected function setUp()
    {
        $this->provider = new CloudbedsProvider([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'mock_redirect_uri',
            'apiBaseUrl'=>'https://hotels.cloudbeds.com/api/v1.1'
        ]);
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);
        $this->assertEquals('/api/v1.1/access_token', $uri['path']);
    }

    public function testGetAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        $this->assertEquals('/api/v1.1/oauth', $uri['path']);
    }

    public function testGetResourceOwnerDetailsUrl()
    {
        $url = $this->provider->getResourceOwnerDetailsUrl($this->getAccessToken());
        $uri = parse_url($url);
        $this->assertEquals('/api/v1.1/userinfo', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $client=$this->mockHttpClient('{"access_token":"mock_access_token","refresh_token":"mock_refresh_token","token_type":"bearer","expires_in":3600}');

        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertEquals('mock_refresh_token', $token->getRefreshToken());
        $this->assertLessThanOrEqual(time() + 3600, $token->getExpires());
        $this->assertGreaterThanOrEqual(time(), $token->getExpires());
        $this->assertNull($token->getResourceOwnerId(), 'Expected null.');
    }

    public function testGetResourceOwner()
    {
        $client=$this->mockHttpClient('{"user_id":"mock_user_id","first_name":"mock_first_name","last_name":"mock_last_name","email":"mock_email"}');
        $this->provider->setHttpClient($client);

        $owner=$this->provider->getResourceOwner($this->getAccessToken());
        $this->assertInstanceOf(CLoudbedsResourceOwner::class,$owner);
        $this->assertEquals($owner->getId(),'mock_user_id');

    }

    private function mockHttpClient($returnData,$code=200)
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeader')
            ->times(1)
            ->andReturn('application/json');
        $response->shouldReceive('getStatusCode')
            ->times(1)
            ->andReturn($code);
        $response->shouldReceive('getBody')
            ->times(1)
            ->andReturn($returnData);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        return $client;
    }

    private function getAccessToken()
    {
        return new AccessToken(['access_token'=>'mock_token','expires_in'=>time()+3600]);
    }

}