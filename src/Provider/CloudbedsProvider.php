<?php
/**
 * Created by PhpStorm.
 * User: rakib
 * Date: 06-May-19
 * Time: 2:36 PM
 */

namespace R4kib\Cloudbeds\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use R4kib\Cloudbeds\Exceptions\CloudbedsHttpException;

class CloudbedsProvider extends AbstractProvider
{
    private $baseUrl;
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->baseUrl= $options['apiBaseUrl'];

    }

    /**
     * Returns the base URL for authorizing a client.
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->baseUrl."/oauth";
    }

    /**
     * Returns the base URL for requesting an access token.
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->baseUrl.'/access_token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->baseUrl.'/userinfo';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        // TODO: Implement getDefaultScopes() method.
    }

    /**
     * Checks a provider response for errors.
     *
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     * @throws CloudbedsHttpException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new CloudbedsHttpException(
                'Cloudbeds API Error ' .  $response->getStatusCode().' - '. $data['message'],
                $response->getStatusCode()
            );
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return CLoudbedsResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new CLoudbedsResourceOwner($response);
    }

    protected function getAuthorizationHeaders($token = null)
    {
        return [ 'Authorization' => 'Bearer ' .$token];
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param  AccessToken $token
     * @return CLoudbedsResourceOwner
*/
    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);

        return $this->createResourceOwner($response, $token);
    }

}