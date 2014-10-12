<?php namespace Atlantis\Api;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider{

    protected $defer = false;


    /**
     * @return void
     */
    public function register(){
        $this->registerConfigOverride();
        $this->registerDependencies();
    }


    /**
     *
     * @return void
     */
    public function boot(){
        $this->package('atlantis/api');
    }


    /**
     *
     * @return void
     */
    public function registerConfigOverride(){
        $this->app['config']->override('api','config',__DIR__.'/../../config/dingo');
    }


    /**
     *
     */
    public function registerDependencies(){
        $this->app->register('Dingo\Api\ApiServiceProvider');
    }


    /**
     * @return array
     */
    public function provides()
    {
        return ['atlantis.api'];
    }
} 