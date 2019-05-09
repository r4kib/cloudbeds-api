<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | www.cloudbeds.com API Credentilals
    |--------------------------------------------------------------------------
    |
    | Here are each of the config for cloudbeds.com API.
    |
    */

    'api' => [
        'clientId' => env('CLOUDBEDS_API_CLIENT_ID'),
        'clientSecret' => env('CLOUDBEDS_API_CLIENT_SECRET'),
        'redirectUri' => env('CLOUDBEDS_API_REDIRECT_URI'),
        'version' => env('CLOUDBEDS_API_VERSION','v1.1')
    ]

];
