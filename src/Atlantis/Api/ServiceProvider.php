<?php namespace Atlantis\API;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider{

    protected $defer = false;


    /**
     * @return void
     */
    public function register(){
        $this->registerConfigOverride();
        //$this->registerDependencies();
        $this->registerServiceRpc();
    }


    /**
     *
     * @return void
     */
    public function boot(){
        $this->package('atlantis/api');

        $this->bootServiceRpc();
    }


    /**
     *
     * @return void
     */
    public function registerConfigOverride(){
        $this->app['config']->override('api','config',__DIR__.'/../../config/dingo/api');
    }


    /**
     *
     */
    public function registerDependencies(){
        $this->app->register('Dingo\Api\ApiServiceProvider');
    }


    /**
     *
     * @return void
     */
    public function registerServiceRpc(){
        $this->app->bind('Atlantis\Api\Rpc\Interfaces\ConfigInterface','Atlantis\Api\Rpc\Config');
        $this->app->bind('Atlantis\Api\Rpc\Interfaces\RouteInterface','Atlantis\Api\Rpc\Route');
    }


    /**
     *
     * @return void
     */
    public function bootServiceRpc(){
        $config = $this->app->make('Atlantis\Api\Rpc\Interfaces\ConfigInterface');
        $route_prefix = $config->getRoutePrefix();

        if (empty($rpc_prefix)) {
            $this->app->before(function(){
                $this->app['router']->post('{all}', function(){
                    $this->app->make('Atlantis\Api\Rpc\Interfaces\RouteInterface')->route();
                })->where('all','*');
            });

        } else {
            $this->app->before(function() use($route_prefix){
                $this->app['router']->post($route_prefix, function(){
                    $this->app->make('Atlantis\Api\Rpc\Interfaces\RouteInterface')->route();
                });
            });
        }
    }


    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }
} 