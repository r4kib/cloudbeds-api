# cloudbeds-api


Cloudbeds.com API Wrapper for PHP with Laravel Integration

## Installation

```
composer require r4kib/cloudbeds-api
```


## Usage

### Initializing The Class

```php
$cloudbeds = new \R4kib\Cloudbeds\Cloudbeds([
    'clientId'                => 'yourId',          // The client ID assigned to you by Amazon
    'clientSecret'            => 'yourSecret',      // The client password assigned to you by Amazon
    'redirectUri'             => 'yourRedirectUri',  // The return URL you specified for your app on Amazon
    'version'                 => 'v1.1'  // API Version, default v1.1
]);
```
### Initializing The Class (Laravel)

1. Publish the configuration
```
php artisan vendor:publish --config
```
2. Setup API details in the `.env` file
```
CLOUDBEDS_API_CLIENT_ID=yourId
CLOUDBEDS_API_CLIENT_SECRET=yourSecret
CLOUDBEDS_API_REDIRECT_URI=yourRedirectUri
CLOUDBEDS_API_VERSION=v1.1
```
3. get the singletone
```php
$cloudbeds = resolve("R4kib\\Cloudbeds\\Cloudbeds");
```
### OAuth Helper
OAuth portion of this implements [thephpleague/oauth2-client](https://github.com/thephpleague/oauth2-client). So for details you should look over there.

```php
$oauthHelper= $cloudbeds->getOauthHelper();
// Get authorization code
if (!isset($_GET['code'])) {
    
    // Get authorization URL
    $authorizationUrl = $oauthHelper->getAuthorizationUrl();

    // Redirect user to authorization URL
    header('Location: ' . $authorizationUrl);
    exit;
} else {
    // Get access token
        $accessToken = $oauthHelper->getAccessToken(
            'authorization_code',
            [
                'code' => $_GET['code']
            ]
        );

    // Get resource owner
        $resourceOwner = $oauthHelper->getResourceOwner($accessToken);
        
    // Now you can store the results to session etc.
    $_SESSION['accessToken'] = $accessToken;
    $_SESSION['resourceOwner'] = $resourceOwner;

    var_dump(
        $resourceOwner->getID(),
        $resourceOwner->getFirstName(),
        $resourceOwner->getLastName(),
        $resourceOwner->getEmail()
    );
}
```
### Make API request
```php
// $params is [key=>value] array. See cloudbeds.com API documentation to view params.
 $cloudbeds->get('/path',$accessToken,$params);
 $cloudbeds->post('/path',$accessToken,$params);
 $cloudbeds->put('/path',$accessToken,$params);
 $cloudbeds->delete('/path',$accessToken,$params);
```


## License

The MIT License (MIT). Please see [License File](http://github.com/r4kib/cloudbeds-api/blob/master/LICENSE) for more information.


 