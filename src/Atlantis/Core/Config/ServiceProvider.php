<?php namespace Atlantis\Core\Config;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Atlantis\Core\Config;


class ServiceProvider extends BaseServiceProvider {

    protected $defer = false;


    public function register(){
        $this->registerConfigProvider();
    }


    public function boot(){
        $this->package('atlantis/core');
    }


    public function registerConfigProvider(){
        #i: Get atlantis config file loader
        $file_loader = new Config\FileLoader($this->app['files'], $this->app['path'].'/config');

        #i: Register the new file loader
        $this->app->instance('config', $config = new Repository(
            $file_loader, $this->app['env']
        ));
    }

}