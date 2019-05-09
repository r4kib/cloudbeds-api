<?php
/**
 * Created by PhpStorm.
 * User: rakib
 * Date: 09-May-19
 * Time: 3:11 AM
 */

namespace R4kib\Cloudbeds\Test;


use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\TestCase;
use R4kib\Cloudbeds\CloudbedsServiceProvider;
use Mockery as m;

class CloudbedsServiceProviderTest extends TestCase
{
    protected $provider;
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $app=m::mock(Application::class);
        $app->shouldReceive('singleton')
        ->times(1)
        ->withAnyArgs()
        ->andReturnNull();
        $app->shouldReceive('publishes')
            ->once()
            ->withAnyArgs()
            ->andReturnNull();
        $app->shouldReceive('make')
            ->with('path.config')
            ->once()
            ->andReturn('/some/path');
       $this->provider= new CloudbedsServiceProvider($app);
    }

    public function testCanBeConstructed()
    {
        $this->assertInstanceOf(CloudbedsServiceProvider::class,$this->provider);
    }

    public function testRegister()
    {
        $this->assertNull($this->provider->register());
    }

//    public function testBoot()
//    {
//        $this->assertNull($this->provider->boot());
//    }
    

}