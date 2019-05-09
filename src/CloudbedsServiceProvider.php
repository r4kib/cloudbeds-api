<?php

namespace R4kib\Cloudbeds;

use Illuminate\Support\ServiceProvider;

class CloudbedsServiceProvider extends ServiceProvider
{
//    protected $defer = true;
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('R4kib\\Cloudbeds\\Cloudbeds', function()
        {
            return new Cloudbeds([
                'clientId' => config('cloudbeds.clientId'),
                'clientSecret' => config('cloudbeds.clientSecret'),
                'redirectUri' =>config('cloudbeds.redirectUri'),
                'version' => config('cloudbeds.version')]) ;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/config/cloudbeds.php' => config_path('cloudbeds.php'),
        ]);
    }
}
