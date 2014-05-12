<?php namespace Atlantis\Core\Config;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Config\Repository;
use Atlantis\Core\Config;


class ServiceProvider extends BaseServiceProvider {

    protected $defer = false;

    public function register(){}

    public function boot(){
        $this->package('atlantis/core');

        #i: Get atlantis config file loader
        $file_loader = new Config\FileLoader($this->app['files'], $this->app['path'].'/config');

        $this->app->instance('config', $config = new Repository(
            $file_loader, $this->app['env']
        ));
    }

}