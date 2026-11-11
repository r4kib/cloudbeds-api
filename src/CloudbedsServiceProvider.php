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
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/cloudbeds.php', 'cloudbeds');

        $this->app->singleton(Cloudbeds::class, function()
        {
            return new Cloudbeds([
                'clientId' => config('cloudbeds.api.clientId'),
                'clientSecret' => config('cloudbeds.api.clientSecret'),
                'redirectUri' =>config('cloudbeds.api.redirectUri'),
                'version' => config('cloudbeds.api.version')]) ;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {

        $this->publishes([
            __DIR__.'/config/cloudbeds.php' => config_path('cloudbeds.php'),
        ], 'cloudbeds-config');
    }
}
