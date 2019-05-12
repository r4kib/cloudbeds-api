<?php
/**
 * Created by PhpStorm.
 * User: rakib
 * Date: 06-May-19
 * Time: 9:14 PM
 */

namespace R4kib\Cloudbeds;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use R4kib\Cloudbeds\Exceptions\CloudbedsHttpException;
use R4kib\Cloudbeds\Exceptions\CloudbedsAPIException;
use R4kib\Cloudbeds\Exceptions\CloudbedsOperationNotSuccessfulException;


class Cloudbeds
{

    private $options;
    private $baseUrl;
    private $httpClient;

    /**
     * @param mixed $httpClient
     */
    public function setHttpClient($httpClient): void
    {
        $this->httpClient = $httpClient;
    }


    /**
     * Cloudbeds constructor.
     * @param $options array
     */
    public function __construct($options)
    {
        $apiEndpoint = 'https://hotels.cloudbeds.com/api/';
        $version = 'v1.1'; //default version
        if (isset($options['version'])) {
            $version = $options['version'];
        }
        $this->baseUrl = $apiEndpoint . $version;
        $options['apiBaseUrl'] = $this->baseUrl;
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }


    /**
     * @return Provider\CloudbedsProvider
     */
    public function getOauthHelper()
    {
        return new Provider\CloudbedsProvider($this->options);
    }

    public function getHttpClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = $this->getOauthHelper()->getHttpClient();
        }
        return $this->httpClient;
    }

    /**
     * @param $uri string
     * @param $accessToken AccessToken
     * @param $params array
     * @return array
     */
    public function get($uri, $accessToken, $params=[])
    {
        return $this->request('GET',$uri,$accessToken,$params);
    }

    /**
     * @param $uri string
     * @param $accessToken AccessToken
     * @param $params array
     * @return array
     */
    public function post($uri, $accessToken, $params=[])
    {
        return $this->request('POST',$uri,$accessToken,$params);
    }

    /**
     * @param $uri string
     * @param $accessToken AccessToken
     * @param $params array
     * @return array
     */
    public function put($uri, $accessToken, $params=[])
    {
        return $this->request('PUT',$uri,$accessToken,$params);
    }

    /**
     * @param $uri string
     * @param $accessToken AccessToken
     * @param $params array
     * @return array
     */
    public function delete($uri, $accessToken, $params=[])
    {
        return $this->request('DELETE',$uri,$accessToken,$params);
    }

    /**
     * @param $method
     * @param $uri
     * @param $accessToken AccessToken
     * @param $params
     * @return array
     * @throws CloudbedsHttpException
     * @throws CloudbedsAPIException
     */
    private function request($method, $uri, $accessToken, $params=[])
    {
        try {
            $response = $this->getHttpClient()->request($method, $this->baseUrl . $uri, $this->makeOption($accessToken, $params));
        } catch (GuzzleException $e) {
            throw new CloudbedsHttpException($e->getMessage(),$e->getCode(),$e->getPrevious());
        }
        $data=json_decode((string)$response->getBody(), true);
        if (isset($data['error'])) {
            throw new CloudbedsAPIException(
                'Cloudbeds API Error ' .  $response->getStatusCode().' - '. $data['message'],
                $response->getStatusCode()
            );
        }
//        if (isset($data['success']) and $data['success']==false) {
//            throw new CloudbedsOperationNotSuccessfulException(
//                'Operation Failed - '. $data['message'],
//                $response->getStatusCode()
//            );
//        }

        return $data;
    }

    /**
     * @param $accessToken AccessToken
     * @param $params array
     * @return array
     */
    private function makeOption($accessToken, $params)
    {
        $options = ['headers' => [
            'Authorization' => 'Bearer ' . $accessToken->getToken()]];

        if (count($params)>0){
            $options['query'] = $params;
        }
        return $options;
    }

}