<?php

namespace Atlantis\Language\Provider;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Atlantis\Language\Environment;

class Laravel extends BaseServiceProvider {

    public function register(){
        $this->app['atlantis.language'] = $this->app->share(function($app){
            return new Environment($app['config']->get('core::language'));
        });
    }

    public function provides(){
        return ['atlantis.language'];
    }

}