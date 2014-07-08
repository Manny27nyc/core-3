<?php namespace Atlantis\Core;
/**
 * Part of the Atlantis package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Atlantis
 * @version    1.0.0
 * @author     Nematix LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 1997 - 2013, Nematix LLC
 * @link       http://nematix.com
 */

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Atlantis\Helpers\String;
use Atlantis\Helpers\Arrays;
use Atlantis\Core\Client\Javascript;
use Atlantis\Core\Client;
use Atlantis\Core\View;
use Atlantis\Core\Config;
use Atlantis\Core\Module;
use Atlantis\Core\Theme;
use Atlantis\Helpers\Environment;


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
        $this->registerDependencies();
        $this->registerCoreServices();
        $this->registerServiceModule();
        $this->registerServiceTheme();
        $this->registerServiceHelpers();
        $this->registerCommands();
        $this->registerAlias();
	}


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('atlantis/core');

        #i: Boot loading
        $this->startLoadModules();
        $this->startLoadSupport();

        #e: Event trigger
        $this->app['events']->fire('atlantis.core.ready');
    }


    /**
     *
     *
     * @return void
     */
    public function registerDependencies(){
        $this->app->register('Atlantis\Core\Config\ServiceProvider');
        $this->app->register('Former\FormerServiceProvider');
    }


    /**
     *
     *
     * @return void
     */
    public function registerCoreServices(){
        $this->app->register('Atlantis\Asset\ServiceProvider');
        $this->app->register('Atlantis\Core\Client\ServiceProvider');
    }


    /**
     *
     *
     * @return void
     */
    public function registerServiceHelpers(){
        $this->app['atlantis.helpers'] = $this->app->share(function($app){
            return new Environment();
        });

        $this->app['atlantis.helpers']->extend('string', new String());
        $this->app['atlantis.helpers']->extend('arrays', new Arrays());
    }


    /**
     *
     *
     * @return void
     */
    public function registerServiceModule(){
        #i: Registering Module environment for facade
        $this->app['atlantis.module'] = $this->app->share(function($app){
            return new Module\Environment($app);
        });
    }


    /**
     *
     *
     * @return void
     */
    public function registerServiceTheme(){
        #i: Registering Module environment for facade
        $this->app['atlantis.theme'] = $this->app->share(function($app){
            return new Theme\Environment($app['config'],$app['view'],$app['files'],null);
        });
    }


    /**
     *
     *
     * @return void
     */
    public function registerAlias(){
        $alias = AliasLoader::getInstance();

        $alias->alias('Former','Former\Facades\Former');
    }


    /**
     *
     *
     * @return void
     */
    public function startLoadModules(){
        $this->app['atlantis.module']->register();
    }


    /**
     *
     *
     * @return void
     */
    public function startLoadSupport(){
        #i: Load events listener
        include __DIR__.'/../../events.php';

        #i: Load views composer
        include __DIR__.'/../../views.php';
    }


    /**
     *
     *
     * @return void
     */
    public function registerCommands(){
        $this->app['atlantis.commands.module-migrate'] = $this->app->share(function($app){
            return new Module\Commands\ModuleCommandMigrate($app['atlantis.module']);
        });

        $this->app['atlantis.commands.module-seed'] = $this->app->share(function($app){
            return new Module\Commands\ModuleCommandSeed($app['atlantis.module']);
        });

        $this->app['atlantis.commands.module-seed-make'] = $this->app->share(function($app){
            return new Module\Commands\ModuleCommandSeedMake($app['atlantis.module'],$app['files']);
        });

        $this->app['atlantis.commands.module-controller'] = $this->app->share(function($app){
            return new Module\Commands\ModuleCommandController($app['atlantis.module']);
        });

        $this->app['atlantis.commands.module-make'] = $this->app->share(function($app){
            return new Module\Commands\ModuleCommandMake($app['atlantis.module'],$app['files']);
        });

        $this->commands(
            'atlantis.commands.module-migrate',
            'atlantis.commands.module-seed',
            'atlantis.commands.module-seed-make',
            'atlantis.commands.module-controller',
            'atlantis.commands.module-make'
        );
    }


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('atlantis.helpers','atlantis.module','atlantis.theme');
	}

}
