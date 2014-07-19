<?php namespace Atlantis\API;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider{

    protected $defer = false;


    /**
     * @return void
     */
    public function register(){
        $this->registerConfigOverride();
    }


    /**
     *
     * @return void
     */
    public function registerConfigOverride(){
        $this->app['config']->override('api','config',__DIR__.'/../../config/dingo-api');
    }
} 