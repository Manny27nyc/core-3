<?php namespace Atlantis\Core;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Atlantis\Core\Client\Javascript;
use Atlantis\Core\View;
use Atlantis\Core\Config;


class CoreServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bind('atlantis.client.javascript',function($app){
            #i: Get configs
            $view = $app['config']->get('core::client.javascript.bind');
            $namespace = $app['config']->get('core::client.javascript.namespace');

            #i: Get view binder
            $binder = new View\Binder($app['events'],$view);

            #i: Return provider instance
            return new Javascript\Provider($binder,$namespace);
        });

        $this->app->bind('atlantis.config.fileloader', function($app){
            #i: Get atlantis config file loader
            $file_loader = new Config\FileLoader($app['files'], $app['path'].'/config');

            #i: Replace default file loader
            $app['config']->setLoader($file_loader);
        });
	}

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('atlantis/core');

        #i: Automatic Alias loader
        AliasLoader::getInstance()->alias(
            'Javascript',
            'Atlantis\Core\Client\Facades\Javascript'
        );

        #i: Activate Atlantis Config Fileloader
        if( $this->app['config']->get('core::app.config.enable',true) ){
            $this->app['atlantis.config.fileloader'];
        }

        #e: Event trigger
        $this->app['events']->fire('atlantis.core.ready');
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('atlantis.client.javascript','atlantis.config.fileloader');
	}

}
