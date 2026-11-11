<?php

namespace R4kib\Cloudbeds\Test;

use Orchestra\Testbench\TestCase;
use R4kib\Cloudbeds\Cloudbeds;
use R4kib\Cloudbeds\CloudbedsServiceProvider;

class CloudbedsServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [CloudbedsServiceProvider::class];
    }

    public function testProviderResolvesConfiguredSingleton(): void
    {
        $this->assertSame('v1.1', config('cloudbeds.api.version'));
        config()->set('cloudbeds.api.clientId', 'test-client');
        config()->set('cloudbeds.api.version', 'v2');

        $client = $this->app->make(Cloudbeds::class);

        $this->assertSame($client, $this->app->make(Cloudbeds::class));
        $this->assertSame('test-client', $client->getOptions()['clientId']);
        $this->assertSame('https://hotels.cloudbeds.com/api/v2', $client->getOptions()['apiBaseUrl']);
    }
}
