<?php
/**
 * Created by PhpStorm.
 * User: rakib
 * Date: 06-May-19
 * Time: 9:11 PM
 */

namespace R4kib\Cloudbeds\Test;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use R4kib\Cloudbeds\Cloudbeds;
use R4kib\Cloudbeds\Provider\CloudbedsProvider;
use R4kib\Cloudbeds\Exceptions\CloudbedsAPIException;
use Mockery as m;

class CloudbedsTest extends TestCase
{
    protected  $cloudbeds;
    protected function setUp()
    {
        $this->cloudbeds = new Cloudbeds([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'mock_redirect_uri',
            'version' => 'v1.1']);
    }

    public function testGetOauthHelper()
    {
        $oauthHelper=$this->cloudbeds->getOauthHelper();
        $this->assertInstanceOf(CloudbedsProvider::class,$oauthHelper);
        $this->assertNotNull($oauthHelper);

    }

    public function testGetOptions()
    {
        $options=$this->cloudbeds->getOptions();
        $this->assertArrayHasKey('apiBaseUrl',$options);
        $uri = parse_url($options['apiBaseUrl']);
        $this->assertEquals('/api/v1.1',$uri['path']);
    }

    public function testGetHttpClient()
    {
        $httpClient=$this->cloudbeds->getHttpClient();
        $this->assertInstanceOf(Client::class,$httpClient);
    }

    public function testGet()
    {
        $this->assertTrue(method_exists($this->cloudbeds,'get'));

        $client=$this->mockHttpClient(200);
        $this->cloudbeds->setHttpClient($client);

        $got=$this->cloudbeds->get('mock_uri','mock_access_token',['mock_param'=>'mock_value']);
        $this->assertIsArray($got);
        $this->assertArrayHasKey('success',$got);
    }
    public function testPost()
    {
        $this->assertTrue(method_exists($this->cloudbeds,'post'));

        $client=$this->mockHttpClient(200);
        $this->cloudbeds->setHttpClient($client);

        $got=$this->cloudbeds->post('mock_uri','mock_access_token',['mock_param'=>'mock_value']);
        $this->assertIsArray($got);
        $this->assertArrayHasKey('success',$got);
    }
    public function testDelete()
    {
        $this->assertTrue(method_exists($this->cloudbeds,'delete'));

        $client=$this->mockHttpClient(200);
        $this->cloudbeds->setHttpClient($client);

        $got=$this->cloudbeds->delete('mock_uri','mock_access_token',['mock_param'=>'mock_value']);
        $this->assertIsArray($got);
        $this->assertArrayHasKey('success',$got);
    }
    public function testPut()
    {
        $this->assertTrue(method_exists($this->cloudbeds,'put'));

        $client=$this->mockHttpClient(200);
        $this->cloudbeds->setHttpClient($client);

        $got=$this->cloudbeds->put('mock_uri','mock_access_token',['mock_param'=>'mock_value']);
        $this->assertIsArray($got);
        $this->assertArrayHasKey('success',$got);
    }

    public function testCloudbedsAPIException()
    {
        $this->expectException(CloudbedsAPIException::class);

        $client=$this->mockHttpClient(400,'{"error":true,"message":"mock_error_message"}');
        $this->cloudbeds->setHttpClient($client);
        $this->cloudbeds->get('mock_uri','mock_access_token',['mock_param'=>'mock_value']);
    }

    /**
     * @param $returnCode integer
     * @param string $returnData
     * @return ClientInterface
     */
    private function mockHttpClient($returnCode=200,$returnData='{"success":true}')
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeader')
            ->times(1)
            ->andReturn('application/json');
        $response->shouldReceive('getStatusCode')
            ->times(1)
            ->andReturn($returnCode);
        $response->shouldReceive('getBody')
            ->times(1)
            ->andReturn($returnData);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('request')
            ->times(1)
            ->withAnyArgs()
            ->andReturn($response);

        return $client;

    }
}